let modalConsole = function (config) {
	config = config || {};
	modalConsole.superclass.constructor.call(this, config);
};
Ext.extend(modalConsole, Ext.Component, {
	window: null,
	label: null,
	config: {},
	keys: [],
	current: 0,
	init: false,
	toggle: function(){
		if (!this.window) {
			this.window = MODx.load({
				xtype: 'modalconsole-window',
				url: this.config.connector_url
			});
			if (this.window.getHeight() < 200) {
				this.window.setHeight(document.body.clientHeight-55);
			}
			this.window.el.setRight(-this.window.getWidth()).setTop(55).setVisible(true, false);
		}

		if (!this.window.isVisible) {
			this.window.isVisible = true;
			this.window.el.addClass('visible').setOpacity(1, {duration: .7, easing: 'easeIn'})
			// this.window.editor.getEl().focus(1000);
		} else {
			this.window.isVisible = false;
			this.window.el.removeClass('visible').setOpacity(0.5, {duration: .7, easing: 'easeIn'}).setRight(-this.window.getWidth());
		}
	},

});

modalConsole = new modalConsole();

/** ************************************************ **/

let modalConsoleWindow = function (config) {
	config = config || {};

	Ext.applyIf(config, {
		renderTo: 'modx-body-tag',
		// renderTo: 'modx-content',
		title: _('modalconsole'),
		id: 'modalconsole-window',
		width: 950,
		minHeight: 200,
		minWidth: 300,
		autoScroll: false,
		// closeAction: 'hide',
		hideMode: 'display',
		isVisible: false,
		tools: [],
		tbar: [{
			xtype: 'button',
			id: 'modalconsole-open',
			cls: 'toolbar-btn',
			text: '<i class="icon icon-folder-open-o"></i>',
			tooltipType: 'title',
			tooltip: _('modalconsole_btn_open'),
			disabled: true,
			handler: function () {
				this.openFile();
			},
		}, {
			xtype: 'button',
			cls: 'toolbar-btn',
			id: 'modalconsole-save',
			text: '<i class="icon icon-save"></i>',
			tooltipType: 'title',
			tooltip: _('modalconsole_btn_save'),
			disabled: true,
			handler: function () {
				this.saveFile();
			},
		}, {
			xtype: 'button',
			cls: 'toolbar-btn',
			id: 'modalconsole-clear',
			text: '<i class="icon icon-eraser"></i>',
			tooltipType: 'title',
			tooltip: _('modalconsole_btn_clear'),
			handler: function () {
				this.clearContent();
			},
			scope: this
		}, {
			xtype: 'tbspacer',
			width: 10
		}, {
			xtype: 'button',
			cls: 'toolbar-btn',
			id: 'modalconsole-collapse',
			text: '<i class="icon icon-columns"></i>',
			tooltipType: 'title',
			tooltip: _('modalconsole_btn_collapse'),
			handler: function () {this.collapsePanel();},
			scope: this
		}, {
			xtype: 'tbspacer',
			width: 10
		}, {
			xtype: 'button',
			cls: 'toolbar-btn',
			id: 'modalconsole-history-prev',
			text: '<i class="icon icon-arrow-left"></i>',
			tooltipType: 'title',
			tooltip: _('modalconsole_btn_history_prev'),
			handler: function () {this.historyPrev();},
			disabled: true,
			scope: this
		}, {
			xtype: 'button',
			cls: 'toolbar-btn',
			id: 'modalconsole-history-next',
			text: '<i class="icon icon-arrow-right"></i>',
			tooltipType: 'title',
			tooltip: _('modalconsole_btn_history_next'),
			disabled: true,
			handler: function () {this.historyNext();},
			scope: this
		}, {
			xtype: 'button',
			cls: 'toolbar-btn',
			id: 'modalconsole-history-clear',
			text: '<i class="icon icon-trash"></i>',
			tooltipType: 'title',
			tooltip: _('modalconsole_btn_history_clear'),
			disabled: !!modalConsole.keys.length,
			handler: function () {this.clearHistory();},
			scope: this
		}, {
			xtype: 'xcheckbox',
			boxLabel: _('modalconsole_save_code'),
			// boxLabel: 'Сохранить код',
			cls: 'toolbar-checkbox',
			id: 'modalconsole-save-code',
			disabled: !modalConsole.config.limit,
			checked: this.saveCodeState()
		}],
		items: [{
			xtype: 'panel',
			// hideLabel: true,
			id: 'modalconsole-console',
			height: 400,
			autoWidth: true,
			// autoHeight: true,
			layout: 'border',
			items: [{
				region: 'center',
				id: 'modalconsole-code',
				// autoScroll: true,
				// unstyled: true,
				bodyStyle: 'background-color:#fff;',
				items: [{
					xtype: Ext.ComponentMgr.types['modx-texteditor'] ? 'modx-texteditor' : 'textarea',
					// xtype: 'textarea',
					editor: null,
					id: 'modalconsole-editor',
					mimeType: 'application/x-php',
					height: '100%',
					value: this.getHistory(),
					enableKeyEvents: true,
					listeners: {
						keydown: function(editor, e) {
							if (e && e.ctrlKey && e.keyCode == e.ENTER) {
								this.execute();
							}
						},
						scope: this
					}
				}]
				//width: 400
			}, {
				// title: '',
				region: 'east',
				id: 'modalconsole-result',
				width: 100,
				split: true,
				collapsible: false,
				minSize: 5,
				maxSize: 0,
				bodyStyle: 'background-color:#fafafa;',
				listeners:{
					"render": {
						fn: function(el){
							el.getUpdater().on('update',function(result, response) {
								try {
									var rObject = JSON.parse(response.responseText);
								} catch (Error) {
									el.update(response.responseText);
									console.warn(Error.message);
									return;
								}
								if (rObject.success) {
									el.update(rObject.output);
								} else {
									MODx.msg.alert(_('error'), rObject.message, Ext.emptyFn);
								}
								// Если код новый
								if (rObject.keys > modalConsole.keys) {
									Ext.getCmp('modalconsole-history-next').disable();
									modalConsole.keys = rObject.keys;
									modalConsole.current = rObject.keys.length - 1;
									(rObject.keys.length > 0) ? Ext.getCmp('modalconsole-history-clear').enable() : Ext.getCmp('modalconsole-history-clear').disable();
									(rObject.keys.length > 1) ? Ext.getCmp('modalconsole-history-prev').enable() : Ext.getCmp('modalconsole-history-prev').disable();
								}
								this.parseProfile(rObject);
								if (el.collapsed) el.toggleCollapse();
							}, this);
						}
					}
					,scope: this
				}
			}],
			listeners:{
				"beforerender": {
					fn: function(el){
						//el.setHeight(this.getHeight()-113);
					}
				}
				,scope: this
			}
			// height: 550,
		}],
		buttonAlign: "left",
		buttons: [{
			xtype: 'modalConsole-profile-label',
			id: 'modalconsole-result-queries',
			tagTitle: 'SQL queries',
			profileName: 'Queries',
			initValue: '0'
		}, {
			xtype: 'modalConsole-profile-label',
			id: 'modalconsole-result-time',
			tagTitle: 'SQL time / PHP time / Total time',
			profileName: 'Time',
			initValue: '0 s / 0 s / 0 s'
		}, {
			xtype: 'modalConsole-profile-label',
			id: 'modalconsole-result-memory',
			tagTitle: 'Current memory / Memory peak',
			profileName: 'Memory',
			initValue: '0 MB / 0 MB'
		}, '->' , {
			text: _("modalconsole_close") ? _("modalconsole_close") : 'Close',
			cls: 'modalconsole-window-btn',
			handler: function () {modalConsole.toggle();},
			scope: this
		}, {
			text: _("modalconsole_btn_execute") ? _("modalconsole_btn_execute") : 'Execute',
			cls: 'modalconsole-window-btn modalconsole-exec-btn',
			handler: function () {this.execute();},
			tooltipType: 'title',
			tooltip: '<ctrl>+<Enter>',
			scope: this
		}],
		keys: [{
			key: Ext.EventObject.ESC,
			shift: false,
			fn: function () {
				modalConsole.toggle();
			},
			scope: this
		}],
		onEsc: modalConsole.toggle,
		listeners: {
			'maximize': function (w) {
				w.el.setTop(55);
			},
			'hide': function () {
				modalConsole.toggle();
			},
			'render': function(w) {
				let el = w.el.select('.x-tool-close').first();
				el.on('click', function (e) {
					modalConsole.toggle();
				});
			},
			'beforerender': function(w) {
				this.codePanel = Ext.getCmp('modalconsole-code');
				this.editor = Ext.getCmp('modalconsole-editor');
				this.resultPanel = Ext.getCmp('modalconsole-result');
			},
			'resize': function(o,w,h) {
				let p = Ext.getCmp('modalconsole-console');
				if (p) {
					p.setHeight(h-113);
					if (this.editor && this.editor.editor) this.editor.editor.resize();
				}
			}
		}
	});
	modalConsoleWindow.superclass.constructor.call(this, config);
};
Ext.extend(modalConsoleWindow, MODx.Window, {
	init: function(response) {
		switch (response.keys.length) {
			case 0:
				Ext.getCmp('modalconsole-history-clear').disable();
			case 1:
				Ext.getCmp('modalconsole-history-prev').disable();
				break;
			default:
				Ext.getCmp('modalconsole-history-prev').enable();
				modalConsole.keys = response.keys;
				modalConsole.current = response.keys.length - 1;
		}
		/*if (response.keys.length <= 1) {
			Ext.getCmp('modalconsole-history-prev').disable();
		} else {
			Ext.getCmp('modalconsole-history-prev').enable();
			modalConsole.keys = response.keys;
			modalConsole.current = response.keys.length - 1;
		}*/
		modalConsole.init = true;
	},
	getHistory: function(key) {
		MODx.Ajax.request({
			url: modalConsole.config.connectorUrl,
			params: {
				action: 'gethistory',
				key: key = key || ''
			},
			listeners: {
				success: {
					fn: function(response) {
						if (response.success) {
							if (!modalConsole.init) this.init(response);
							this.editor.setValue(response.code);
						} else {
							this.resultPanel.update(response.message);
						}
					},
					scope: this
				},
				failure: function(response) {console.log(response);}
			}
		});
		return "<?php\n"
	},
	collapsePanel: function(){
		this.resultPanel.toggleCollapse();
	},
	clearContent: function(){
		this.editor.setValue('<?php\n');
		this.resultPanel.update('');
	},
	historyPrev: function(){
		Ext.getCmp('modalconsole-history-next').enable();
		modalConsole.current--;
		if (modalConsole.current == 0) {
			Ext.getCmp('modalconsole-history-prev').disable();
		}
		this.getHistory(modalConsole.keys[modalConsole.current]);
	},
	historyNext: function(){
		Ext.getCmp('modalconsole-history-prev').enable();
		modalConsole.current++;
		if (modalConsole.current >= modalConsole.keys.length - 1) {
			Ext.getCmp('modalconsole-history-next').disable();
		}
		this.getHistory(modalConsole.keys[modalConsole.current]);
	},
	clearHistory: function(){
		MODx.Ajax.request({
			url: modalConsole.config.connectorUrl,
			params: {
				action: 'clearhistory'
			},
			listeners: {
				success: {
					fn: function(response) {
						if (response.success) {
							Ext.getCmp('modalconsole-history-prev').disable();
							Ext.getCmp('modalconsole-history-next').disable();
							Ext.getCmp('modalconsole-history-clear').disable();
							modalConsole.keys = [];
							modalConsole.current = 0;
							// this.editor.setValue("<?php\n");
						} else {
							MODx.msg.alert(_('error'), response.message, Ext.emptyFn);
							// this.resultPanel.update(response.message);
						}
					},
					scope: this
				},
				failure: {
					fn: function (response) {
						MODx.msg.alert(_('error'), response.message, Ext.emptyFn);
					}
				}
			}
		});
	},
	execute: function(){
		//this.resultPanel.el.mask(_('working'));
		let state = +Ext.getCmp('modalconsole-save-code').checked;
		Ext.util.Cookies.set('modalconsoleSaveCode', state);
		let code = this.editor.getValue().replace(/^\s*<\?(php)?\s*/mi, '').trim();
		if (code) {
			let updater = this.resultPanel.getUpdater();
			updater.timeout = 0;
			updater.update({
				url: modalConsole.config.connectorUrl,
				params:{
					action: 'exec',
					code: code,
					save: state
				}
			});
		}
	},
	parseProfile: function (result) {
		for (let key in result.profile) {
			let pItem = Ext.getCmp('modalconsole-result-' + key);
			pItem.parseData(result.profile[key]).update(pItem.html);
		}
	},
	saveCodeState: function() {
		let state = Ext.util.Cookies.get('modalconsoleSaveCode');
		if (state === null) {
			state = !!modalConsole.config.limit;
			Ext.util.Cookies.set('modalconsoleSaveCode', +state);
		}
		return +state;
	},
	openFile: function () {},
	saveFile: function () {},

});

Ext.reg('modalconsole-window', modalConsoleWindow);

/** ******************************************************* **/
modalConsole.label = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		tag: 'span',
		tagCls: 'profile-item',
		listeners:{
			"beforerender": {
				fn: function(el){
					el.parseData()
				}
			}
			,scope: this
		}
	});
	modalConsole.label.superclass.constructor.call(this,config);
};
Ext.extend(modalConsole.label, Ext.form.Label, {
	parseData: function (value) {
		value = value || this.initValue;

		this.html = '{profileName}: <{tag} class="{class}" title="{title}">{value}</{tag}>'
			.replace('{profileName}', this.profileName)
			.replace(/{tag}/g, this.tag)
			.replace('{class}', this.tagCls)
			.replace('{title}', this.tagTitle)
			.replace('{value}', value);
		return this;
	}
});
Ext.reg('modalConsole-profile-label', modalConsole.label);

/** ***************************************************/
Ext.onReady(function() {
	let usermenuUl = document.getElementById("modx-user-menu"),
		firstLi = usermenuUl.firstChild,
		modalconsoleLi = document.createElement("LI"),
		title = _('modalconsole_open_console');

	modalconsoleLi.id = "modalconsole-li";
	modalconsoleLi.innerHTML = "<a id=\"modalconsole-link\" class=\"modalconsole\" href=\"javascript:;\" onclick=\"modalConsole.toggle()\" title=\""+ title +"\"><i class=\"icon icon-terminal\"></i></a>";
	usermenuUl.insertBefore(modalconsoleLi, firstLi);
});