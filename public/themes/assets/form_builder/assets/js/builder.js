	
	var create_form_code;
	$( document ).ready(function() {
		//Create form code
		create_form_code=function(test_data){ //test_data needs to be JavaScript object
			var extras=$(".extra-settings").serializeObject();
			extras['emailto']=$("#emailto").val();	
			var form_data=$.extend({}, JSON.parse(formjson), extras);	
			if (test_data){
				form_structure=JSON.stringify(test_data);}
			else{
				form_structure=JSON.stringify(form_data);
			}			
			console.log('start');
			
			$("#form_builder_fields").val(form_structure);
			
		}
			
		//upon page load
		var builder = new EditorViewModel(initial_form);
		ko.applyBindings(builder);
		create_form_code();
		var old_extras=$(".extra-settings").serializeObject();
		old_extras=JSON.stringify(old_extras);
		var old_form_json=formjson;
		changes_exist=function(){
			var extras=$(".extra-settings").serializeObject();
			extras=JSON.stringify(extras);		
			if (old_form_json==formjson && extras==old_extras){
				return false
			}
			else{
				old_extras=extras;
				old_form_json=formjson;
				return true
			}
		}		
		
		//Set the CSS for the font size
		var font_size=$(".extra-settings").serializeObject().font_size;
		if (font_size=='14' || !font_size){font_size=''}
		$('#dynamic_bootstrap').remove();
		var stylesheet='<link id="dynamic_bootstrap" rel="stylesheet" href="http://localhost:8012/test/form-builder/static/cdn/bootstrap-iso'+font_size+'.css">';
		$('head').append(stylesheet);
		//End set CSS
		

		$("#add_a_field .btn").click(function(event) {
			create_form_code();
		});			
		
		setInterval(function(){
			if (changes_exist()){
				create_form_code();
			}
		}, 1500);
		
		$("#emailto").change(function() {
			create_form_code();
		});
		
		
		$("select[name='font_size']").change(function() {
			var font_size=$(this).val();
			if (font_size=='14' || !font_size){font_size=''}
			$('#dynamic_bootstrap').remove();
			var stylesheet='<link id="dynamic_bootstrap" rel="stylesheet" href="/static/cdn/bootstrap-iso'+font_size+'.css">';
			$('head').append(stylesheet);
		});		
		

		var picker_opts= {
		title: false, // Popover title (optional) only if specified in the template
		selected: false, // use this value as the current item and ignore the original
		defaultValue: false, // use this value as the current item if input or element value is empty
		placement: 'top', // (has some issues with auto and CSS). auto, top, bottom, left, right
		collision: 'none', // If true, the popover will be repositioned to another position when collapses with the window borders
		animation: true, // fade in/out on show/hide ?
		//hide iconpicker automatically when a value is picked. it is ignored if mustAccept is not false and the accept button is visible
		hideOnSelect: true,
		showFooter: false,
		searchInFooter: false, // If true, the search will be added to the footer instead of the title
		mustAccept: false, // only applicable when there's an iconpicker-btn-accept button in the popover footer
		fullClassFormatter: function(val) {
			return 'fa ' + val;
		},
		icons: ["500px","amazon","balance-scale","battery-empty","battery-quarter","battery-half","battery-three-quarters","battery-full","black-tie","calendar-check-o","calendar-minus-o","calendar-plus-o","calendar-times-o","cc-diners-club","cc-jcb","chrome","clone","commenting","commenting-o","contao","creative-commons","expeditedssl","firefox","fonticons","genderless","get-pocket","gg","gg-circle","hand-rock-o","hand-lizard-o","hand-paper-o","hand-peace-o","hand-pointer-o","hand-scissors-o","hand-spock-o","hourglass","hourglass-start","hourglass-half","hourglass-end","hourglass-o","houzz","i-cursor","industry","internet-explorer","map","map-o","map-pin","map-signs","mouse-pointer","object-group","object-ungroup","odnoklassniki","odnoklassniki-square","opencart","opera","optin-monster","registered","safari","sticky-note","sticky-note-o","television","trademark","tripadvisor","vimeo","wikipedia-w","y-combinator","adjust","anchor","archive","area-chart","arrows","arrows-h","arrows-v","asterisk","at","car","ban","university","bar-chart","barcode","bars","bed","beer","bell","bell-o","bell-slash","bell-slash-o","bicycle","binoculars","birthday-cake","bolt","bomb","book","bookmark","bookmark-o","briefcase","bug","building","building-o","bullhorn","bullseye","bus","taxi","calculator","calendar","calendar-o","camera","camera-retro","caret-square-o-down","caret-square-o-left","caret-square-o-right","caret-square-o-up","cart-arrow-down","cart-plus","cc","certificate","check","check-circle","check-circle-o","check-square","check-square-o","child","circle","circle-o","circle-o-notch","circle-thin","clock-o","times","cloud","cloud-download","cloud-upload","code","code-fork","coffee","cog","cogs","comment","comment-o","comments","comments-o","compass","copyright","credit-card","crop","crosshairs","cube","cubes","cutlery","tachometer","database","desktop","diamond","dot-circle-o","download","pencil-square-o","ellipsis-h","ellipsis-v","envelope","envelope-o","envelope-square","eraser","exchange","exclamation","exclamation-circle","exclamation-triangle","external-link","external-link-square","eye","eye-slash","eyedropper","fax","rss","female","fighter-jet","file-archive-o","file-audio-o","file-code-o","file-excel-o","file-image-o","file-video-o","file-pdf-o","file-powerpoint-o","file-word-o","film","filter","fire","fire-extinguisher","flag","flag-checkered","flag-o","flask","folder","folder-o","folder-open","folder-open-o","frown-o","futbol-o","gamepad","gavel","gift","glass","globe","graduation-cap","users","hdd-o","headphones","heart","heart-o","heartbeat","history","home","picture-o","inbox","info","info-circle","key","keyboard-o","language","laptop","leaf","lemon-o","level-down","level-up","life-ring","lightbulb-o","line-chart","location-arrow","lock","magic","magnet","share","reply","reply-all","male","map-marker","meh-o","microphone","microphone-slash","minus","minus-circle","minus-square","minus-square-o","mobile","money","moon-o","motorcycle","music","newspaper-o","paint-brush","paper-plane","paper-plane-o","paw","pencil","pencil-square","phone","phone-square","pie-chart","plane","plug","plus","plus-circle","plus-square","plus-square-o","power-off","print","puzzle-piece","qrcode","question","question-circle","quote-left","quote-right","random","recycle","refresh","retweet","road","rocket","rss-square","search","search-minus","search-plus","server","share-alt","share-alt-square","share-square","share-square-o","shield","ship","shopping-cart","sign-in","sign-out","signal","sitemap","sliders","smile-o","sort","sort-alpha-asc","sort-alpha-desc","sort-amount-asc","sort-amount-desc","sort-asc","sort-desc","sort-numeric-asc","sort-numeric-desc","space-shuttle","spinner","spoon","square","square-o","star","star-half","star-half-o","star-o","street-view","suitcase","sun-o","tablet","tag","tags","tasks","terminal","thumb-tack","thumbs-down","thumbs-o-down","thumbs-o-up","thumbs-up","ticket","times-circle","times-circle-o","tint","toggle-off","toggle-on","trash","trash-o","tree","trophy","truck","tty","umbrella","unlock","unlock-alt","upload","user","user-plus","user-secret","user-times","video-camera","volume-down","volume-off","volume-up","wheelchair","wifi","wrench","hand-o-down","hand-o-left","hand-o-right","hand-o-up","ambulance","subway","train","transgender","mars","mars-double","mars-stroke","mars-stroke-h","mars-stroke-v","mercury","neuter","transgender-alt","venus","venus-double","venus-mars","file","file-o","file-text","file-text-o","cc-amex","cc-discover","cc-mastercard","cc-paypal","cc-stripe","cc-visa","google-wallet","paypal","btc","jpy","usd","eur","gbp","ils","inr","krw","rub","try","align-center","align-justify","align-left","align-right","bold","link","chain-broken","clipboard","columns","files-o","scissors","outdent","floppy-o","font","header","indent","italic","list","list-alt","list-ol","list-ul","paperclip","paragraph","repeat","undo","strikethrough","subscript","superscript","table","text-height","text-width","th","th-large","th-list","underline","angle-double-down","angle-double-left","angle-double-right","angle-double-up","angle-down","angle-left","angle-right","angle-up","arrow-circle-down","arrow-circle-left","arrow-circle-o-down","arrow-circle-o-left","arrow-circle-o-right","arrow-circle-o-up","arrow-circle-right","arrow-circle-up","arrow-down","arrow-left","arrow-right","arrow-up","arrows-alt","caret-down","caret-left","caret-right","caret-up","chevron-circle-down","chevron-circle-left","chevron-circle-right","chevron-circle-up","chevron-down","chevron-left","chevron-right","chevron-up","long-arrow-down","long-arrow-left","long-arrow-right","long-arrow-up","backward","compress","eject","expand","fast-backward","fast-forward","forward","pause","play","play-circle","play-circle-o","step-backward","step-forward","stop","youtube-play","adn","android","angellist","apple","behance","behance-square","bitbucket","bitbucket-square","buysellads","codepen","connectdevelop","css3","dashcube","delicious","deviantart","digg","dribbble","dropbox","drupal","empire","facebook","facebook-official","facebook-square","flickr","forumbee","foursquare","git","git-square","github","github-alt","github-square","gratipay","google","google-plus","google-plus-square","hacker-news","html5","instagram","ioxhost","joomla","jsfiddle","lastfm","lastfm-square","leanpub","linkedin","linkedin-square","linux","maxcdn","meanpath","medium","openid","pagelines","pied-piper","pied-piper-alt","pinterest","pinterest-p","pinterest-square","qq","rebel","reddit","reddit-square","renren","sellsy","shirtsinbulk","simplybuilt","skyatlas","skype","slack","slideshare","soundcloud","spotify","stack-exchange","stack-overflow","steam","steam-square","stumbleupon","stumbleupon-circle","tencent-weibo","trello","tumblr","tumblr-square","twitch","twitter","twitter-square","viacoin","vimeo-square","vine","vk","weixin","weibo","whatsapp","windows","wordpress","xing","xing-square","yahoo","yelp","youtube","youtube-square","h-square","hospital-o","medkit","stethoscope","user-md"],	
        templates: {
            popover: '<div class="iconpicker-popover popover"><div class="arrow"></div>' + '<div class="popover-title"></div><div class="popover-content"></div></div>',
            footer: '<div class="popover-footer"></div>',
            buttons: '<button class="iconpicker-btn iconpicker-btn-cancel btn btn-default btn-sm">Cancel</button>' + ' <button class="iconpicker-btn iconpicker-btn-accept btn btn-primary btn-sm">Accept</button>',
            search: '<input type="text" class="form-control iconpicker-search" placeholder="Type to filter" />',
            iconpicker: '<div class="iconpicker"><div class="iconpicker-items"></div></div>',
            iconpickerItem: '<div class="iconpicker-item"><i></i></div>'
        },		
		input: 'input,.iconpicker-input', // children input selector
		inputSearch: false, // use the input as a search box too?
		container: false, //  Appends the popover to a specific element. If not set, the selected element or element parent is used
		component: '.input-group-addon,.iconpicker-component', // children component jQuery selector or object, relative to the container elemen
		};		
		
		
		// var field_settings = $('#left-tabs a[href="#field-settings-pane"]');
		// field_settings.on('show.bs.tab', function (e) {
			// tab_active();
		// })
		
		$('#tab_editor').on('click', '.click_on_field', function(e){
			tab_active();	
		})

		
		function tab_active(){
			//make sure that the icon picker is here!
			var prepend_icon=$('input[name=prepend_icon], input[name=append_icon]');
			if(prepend_icon.length){
				setTimeout(function(){ prepend_icon.iconpicker(picker_opts);}, 500);
				prepend_icon.on('iconpickerSelected', function(e) {
					fire_change(e.target); //tells ko.js that we've updated things
					return false
				});
			}
			
			var append_icon=$('input[name=append_icon], input[name=append_icon]');
			if(append_icon.length){
				setTimeout(function(){ append_icon.iconpicker(picker_opts);}, 500);
				append_icon.on('iconpickerSelected', function(e) {
					fire_change(e.target); //tells ko.js that we've updated things
					return false
				});	
			}
		}
		
		function fire_change(element){
			if ("createEvent" in document) {
				var evt = document.createEvent("HTMLEvents");
				evt.initEvent("change", false, true);
				element.dispatchEvent(evt);
			}
			else{
				element.fireEvent("onchange");		
			}
		}		
		
		
		
	});	

	