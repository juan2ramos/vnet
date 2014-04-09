var tour_mentor_experto = [
{
	hookTo: '',
	content: '#box_intro_m',
	width: 400,
	arrowPosition: 'sc',
	showArrow: false,
	highlight: true,
	time: '00:10',
	customClassStep: style_tour,
	onHideStep:function(){}
},
{
	hookTo: '#bar_mensajes',
	content: '#bar_box_mensajes_m',
	width: 268,
	arrowPosition: 'bm',
	showArrow: true,
	highlight: true,
	highlightElements: '.header',
	time: '00:05',
	customClassStep: style_tour,
	onHideStep:function(){}
},
{
	hookTo: '#bar_contactos',
	content: '#bar_box_contactos_m',
	width: 268,
	arrowPosition: 'rt',
	showArrow: true,
	highlight: true,
	highlightElements: '.header',
	time: '00:05',
	customClassStep: style_tour,
	onShowStep: function(){ $('#bar_usuario').removeClass('open'); },
	onHideStep:function(){}
},

{
	hookTo: '#bar_usuario',
	content: '#bar_box_usuario_mentor',
	width: 268,
	arrowPosition: 'lt',
	showArrow: true,
	highlight: true,
	highlightElements: '.header',
	time: '00:05',
	customClassStep: style_tour,
	onShowStep: function(){ $('#bar_usuario').addClass('open'); },
	onHideStep:function(){}
},

{
	hookTo: '#bar_usuario_perfil',
	content: '#bar_box_usuario_perfil_mentor',
	width: 268,
	offsetX: 30,
	arrowPosition: 'lt',
	showArrow: true,
	highlight: true,
	highlightElements: '.header',
	time: '00:05',
	customClassStep: style_tour,
	onShowStep: function(){ $('#bar_usuario').addClass('open'); },
	onHideStep:function(){}
},
{
	hookTo: '#bar_usuario_resenas',
	content: '#bar_box_usuario_resenas',
	width: 268,
	offsetX: 30,
	arrowPosition: 'lt',
	showArrow: true,
	highlight: true,
	highlightElements: '.header',
	time: '00:05',
	customClassStep: style_tour,
	onShowStep: function(){ $('#bar_usuario').addClass('open'); },
	onHideStep:function(){}
},
{
	hookTo: '#bar_usuario_logout',
	content: '#bar_box_usuario_logout_m',
	width: 268,
	offsetX: 60,
	arrowPosition: 'lt',
	showArrow: true,
	highlight: true,
	highlightElements: '.header',
	time: '00:05',
	customClassStep: style_tour,
	onShowStep: function(){ $('#bar_usuario').addClass('open'); },
	onHideStep:function(){}
},
{
	hookTo: '#menu_agenda_mentor',
	content: '#box_agenda_me',
	width: 268,
	arrowPosition: 'rt',
	showArrow: true,
	highlight: true,
	highlightElements: '#sidebar',
	time: '00:05',
	customClassStep: style_tour,
	onShowStep: function(){	$('#bar_usuario').removeClass('open'); },
	onHideStep:function(){}
},
{
	hookTo: '#menu_mensajes',
	content: '#box_mensajes_m',
	width: 268,
	arrowPosition: 'rt',
	showArrow: true,
	highlight: true,
	highlightElements: '#sidebar',
	time: '00:05',
	customClassStep: style_tour,
	onHideStep:function(){}
},
{
	hookTo: '#menu_foros',
	content: '#box_foros_m',
	width: 268,
	arrowPosition: 'rt',
	showArrow: true,
	highlight: true,
	highlightElements: '#sidebar',
	time: '00:05',
	customClassStep: style_tour,
	onShowStep: function(){ $('#menu_plataforma').removeClass('active'); },
	onHideStep:function(){}
},
{
	hookTo: '',
	content: '#box_end',
	width: 400,
	arrowPosition: 'sc',
	showArrow: false,
	highlight: true,
	time: '00:05',
	customClassStep: style_tour,
	onHideStep:function(){}
}
];
