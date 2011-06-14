var ccmDateNav={
	navs:[],
	currentPg:0,
	currentPgNum:0,
	loadCurrentPage:1,
	init:function(){ 
		this.navs=jQuery('.ccmDateNav');
		this.navs.each(function(i,nav){ 
			nav=jQuery(nav);
			if(nav.prepared) return;
			nav.find('.trigger').each(function(i,trig){
				trig.onclick=function(){
					ccmDateNav.triggered(this); 
				}
			});
			/*
			var pNs=nav.find('.pageNode');
			pNs.each(function(i,n){
				n.pNs=jQuery(pNs);
				jQuery(n).click(function(el){
					el.pNs.removeClass('selected');
					el.addClass('selected');
				})
			}); 
			*/
			nav.prepared=1;
		});  

		this.setPg( this.loadPg, this.dateKey ); 
	},
	triggered:function(trig,mode){ 
		var c='closed',ul=jQuery(trig.parentNode).find('ul');
		var trigEl=jQuery(trig);
		ul=jQuery(ul.get(0));
		if( mode!='close' && (trigEl.hasClass(c) || mode=='open') ){ 
			//jQuery(trig.parentNode.parentNode).find('ul .trigger').addClass(c);
			jQuery(trig.parentNode.parentNode).find('ul').each( function(i,sibling){
				if(sibling!=ul.get(0)){
					jQuery(sibling).hide(500);				
					jQuery(sibling.parentNode).find('.trigger').addClass(c);
				}
			});			
			trigEl.removeClass(c); 
			//animateHelper.scrollOpen(ul);
			ul.show(500);//css('display','block');			
		}else{  
			trigEl.addClass(c);
			//animateHelper.scrollClosed(ul);
			ul.hide(500);//.css('display','none');
		}
	},
	setPg:function( id, dateKey ){  
		var y = dateKey.substr(dateKey.indexOf('_')+1, dateKey.length); 
		this.navs.each( function(i,nav){
			nav=jQuery(nav);
			nav.find('.pageNode').removeClass('selected');
			var p=nav.find('.pageId'+id);
			if(p) p.addClass('selected');
			var trigs=nav.find('.trigger');
			trigs.each(function(i,t){
				trigEl=jQuery(t);
				if( ccmDateNav.loadCurrentPage && trigEl.hasClass('closed') && (trigEl.hasClass('month'+dateKey) || trigEl.hasClass('year'+y)) ){ 
					//alert(trigEl.html() + 'open')
					ccmDateNav.triggered( t ,'open');
				}else if(!ccmDateNav.loadCurrentPage || !trigEl.hasClass('closed') && ( !trigEl.hasClass('month'+dateKey) && !trigEl.hasClass('year'+y) ) ){ 
					//alert(trigEl.html() + 'close')
					ccmDateNav.triggered( t ,'close');
				}
			}); 
		});
		this.loadPg=0;
	},	
	deselectAll:function(){
		this.navs.each( function(i,nav){ nav.find('.pageNode').removeClass('thisPg'); });
	},
	closeAll:function(){ 
		this.navs.each( function(i,nav){
			nav.find('.trigger').each( function(i,trig){ ccmDateNav.triggered(trig,'close'); });	
		});
	}
}