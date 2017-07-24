$(function(){
	$(".user").hover(function(){
		$(this).find(".user-box").slideDown(300)
	},function(){
		$(this).find(".user-box").stop().slideUp(300)
	});
	//登录
	$("#login").on("submit",function(){
		if(!fbUi.expPhone($('[ name="account"]').val())){
			$('[ name="account"]').addClass("input-error");
		}
		if($('[ name="pass"]').val().length < 6){
			$('[ name="pass"]').addClass("input-error");
		}
		return false;
	});
	//注册
	//
	$("#reg").on("submit",function(){

		if(!fbUi.expPhone($('[ name="account"]').val())){
			$('[ name="account"]').addClass("input-error");
		}
		if(!fbUi.expEmpty($('[ name="code"]').val())){
			$('[ name="code"]').addClass("input-error");
		}
		if($('[ name="pass"]').val().length < 6){
			$('[ name="pass"]').addClass("input-error");
		}
		if($('[ name="pass"]').val() != $('[ name="repass"]').val()){
			$('[ name="pass"]').addClass("input-error");
			$('[ name="repass"]').addClass("input-error");
		}
		if(!fbUi.expEmpty($('[ name="user"]').val())){
			$('[ name="user"]').addClass("input-error");
		}
		if(!$('[name="check"]').is(':checked')){
			fbUi.fbNews({"type":"warning","content":"请阅读并同意《 天佑用户协议 》"});
		}
		return false;
	});
	//验证手机号码
	$('[ name="account"]').on("blur",function(){
		if(!fbUi.expPhone($(this).val())){
			if(!$(this).hasClass("input-error"))
				fbUi.fbNews({"type":"warning","content":"请填写正确的手机号码"});
			$(this).addClass("input-error");

		}else{
			$(this).removeClass("input-error");

		}
	});
	//验证验证码
	$('[ name="code"]').on("blur",function(){
		if(!fbUi.expEmpty($(this).val())){
			if(!$(this).hasClass("input-error"))
				fbUi.fbNews({"type":"warning","content":"请填写验证码"});
			$(this).addClass("input-error");

		}else{
			$(this).removeClass("input-error");

		}
	});
	//验证密码
	$('[ name="pass"]').on("blur",function(){
		if($(this).val().length <= 6){
			if(!$(this).hasClass("input-error"))
				fbUi.fbNews({"type":"warning","content":"请填写密码，不可小于6位数"});
			$(this).addClass("input-error");

		}else{
			$(this).removeClass("input-error");
		}
	});
	//验证密码是否相同
	$('[ name="repass"]').on("blur",function(){
		if($(this).val() != $('[ name="pass"]').val()){
			if(!$(this).hasClass("input-error"))
				fbUi.fbNews({"type":"warning","content":"两次密码不一样"});
			$(this).addClass("input-error");
			$('[ name="pass"]').addClass("input-error");
		}else{
			$(this).removeClass("input-error");

		}
	});
	//验证昵称
	$('[ name="user"]').on("blur",function(){
		if(!fbUi.expEmpty($(this).val())){
			if(!$(this).hasClass("input-error"))
				fbUi.fbNews({"type":"warning","content":"请填写昵称"});
			$(this).addClass("input-error");

		}else{
			$(this).removeClass("input-error");

		}
	});
	//注册获取验证码
	$(".reg .getCode").on("click",function(){
		var tell = $("[name='account']").val();
		if(!$fb.expPhone(tell)){
			$fb.fbNews({"type":"danger","content":"手机号码格式错误"});
			return false;
		}
		if($(this).hasClass("active")){
			return false;
		}
		$(this).addClass("active")
		var t = 60;
		fbUi.getCode("http://192.168.0.19",function(){$(".getCode").html("60秒后可重新获取");});
		var time = setInterval(function(){
			--t;
			$(".getCode").html(t+"秒后可重新获取");
			if(t == 0){
				$(".getCode").removeClass("active").html("获取验证码");
				clearInterval(time)
			}
		},1000)

	})
	//修改密码获取验证码
	$(".security .getCode").on("click",function(){
		var tell = $(".security .tell").val();
		if(!$fb.expPhone(tell)){
			$fb.fbNews({"type":"danger","content":"手机号码格式错误"});
			return false;
		}
		if($(this).hasClass("active")){
			return false;
		}
		$(this).addClass("active")
		var t = 60;
		fbUi.getCode("http://192.168.0.19",function(){$(".getCode").html("60秒后可重新获取");});
		var time = setInterval(function(){
			--t;
			$(".getCode").html(t+"秒后可重新获取");
			if(t == 0){
				$(".getCode").removeClass("active").html("获取验证码");
				clearInterval(time)
			}
		},1000)

	})
	//切换

	$(".exService-page-prev").on("mousedown mouseup",function(e){
		if(e.type == 'mousedown'){
			$(this).css({"width":"18px","height":"18px","bottom":"3px"});
			var parent = $(this).parents(".exService-page");
			var count = parseInt(parent.find(".exService-page-item").length/3);
			if(count > 3 ){
				connt = 2;
			}
			var index = parseInt(parent.attr("index"));
			index = --index < 0 ? 0 : index;
			parent.find(".exService-page-box").stop().animate({"left":-522*index},200);
			parent.attr("index",index);
		}else{
			$(this).removeAttr("style");
		}


	})
	$(".exService-page-next").on("mousedown mouseup",function(e){
		if(e.type == 'mousedown'){
			$(this).css({"width":"18px","height":"18px","bottom":"3px"});
			var parent = $(this).parents(".exService-page");
			var count = parseInt(parent.find(".exService-page-item").length/3);
			if(count > 3 ){
				connt = 2;
			}
			var index = parseInt(parent.attr("index"));
			index = ++index > count-1  ? count-1 : index;
			parent.find(".exService-page-box").stop().animate({"left":-522*index},200);
			parent.attr("index",index);
		}else{
			$(this).removeAttr("style");
		}
	})
	$(".exService-page-item").on("click",function(){
		var parent = $(this).parents(".exService");
		var index = $(this).index();
		parent.find(".exService-page-item").eq(index).addClass("active").siblings(".exService-page-item").removeClass("active")
		parent.find(".exService-item").eq(index).fadeIn(200).siblings(".exService-item").hide();
	})

	//作者移入效果
	$(".author .big-author .big-author-item").hover(function(){
		$(this).find(".big-author-bg").animate({"top":0},200)
	},function(){
		$(this).find(".big-author-bg").animate({"top":360},200)
	})
		//筛选
	$(".screen-box input[type='radio']").on("change",function(){
		var name = $(this).attr("name");
		var parents =$(this).parents("form");
		$.each(parents.find("input[name="+name+"]"),function(a,b){
			if($(b).is(":checked")){
				$(b).parent().find("label").addClass("on");
			}else{
				$(b).parent().find("label").removeClass("on");
			}
		})
	})
	$(".close-form").on("click",function(){
		$(this).parents(".screen-box").fadeOut(200);
		$(".clock").fadeOut(200);
		$(".artwork-class .artwork-class-item").removeClass("on");
	});
	$(".artwork-class .artwork-class-item").on("click",function(){
		$(".clock").fadeIn(200)
		var i =$(this).index(".artwork-class-item");
		if($(this).hasClass("on")){
			$(".clock").fadeOut(200);
			$(this).removeClass("on");
			$(".screen-box").fadeOut(200)
			return false;

		}
		$(this).addClass("on").siblings(".artwork-class-item").removeClass("on");
		$(".screen-box").fadeIn(200).find(".screen-item").eq(i).fadeIn(200).siblings(".screen-item").hide()
	})
	//艺术品详情radio
	$("input[name='type']").on("click",function(){
		for(var i = 0,c=$(".worksOperation input").length ; i < c ; i++){

			if($(".worksOperation input").eq(i).is(":checked")){
				$(".worksOperation label").eq(i).addClass("active");
			}else{
				$(".worksOperation label").eq(i).removeClass("active");
			}
		}
	})
	//艺术品收藏
	$(".worksCollection").on("click",function(){
		$(this).toggleClass("active");
	})


	//删除购物车
	$(".cart .delete").on("click",function(){
		$fb.showModal({
		    title:"提示",
		    content:"是否删除该商品？"
		},function(){

		})
	})

	//选择购物车
	$(".cart-checkbox").on("click",function(){
		$(this).toggleClass("checked");
		if($(this).hasClass("checked")){
			$(this).prev().attr("checked",'true');
		}else{
			$(this).prev().removeAttr("checked");
		}
	});
	$(".allCheck").on("click",function(){
		$(this).toggleClass("checked");
		if($(this).hasClass("checked")){
			$(this).parents(".cart").find('[type="checkbox"]').attr("checked",'true');
			$(".cart-checkbox").addClass("checked");
		}else{
			$(this).prev().removeAttr("checked");
			$(this).parents(".cart").find('[type="checkbox"]').removeAttr("checked");
			$(".cart-checkbox").removeClass("checked");
		}
	});
	//选择收货地址
	$(".info-map-item").on("click",function(){

		if($(this).hasClass("checked")){
			return false;
		}else{
			$(this).addClass("checked").siblings(".info-map-item").removeClass("checked");
			$(".info-map-item").find('[type="checkbox"]').removeAttr("checked");
			$(this).find('[type="checkbox"]').attr("checked",'true');

		}
	});
})


//弹出框
	$(function(){
		$('#matter').focus(function(){
			$('.painting').addClass('show_div');
			$('#matter').blur();
});
		var paiting_li = $('.painting >div li');
		paiting_li.on('click',function  () {
			var val = $(this).text();
			console.log(val);
			$('#matter').val(val);
			$('.painting').removeClass('show_div');

		})

		// 下拉框
		$('body').on('focus','.selectBox_input',function(){
			var parents = $(this).parents(".selectBox_all");
			$('.selectBox').fadeOut(50);
			$(".author-box").fadeOut(50)

			parents.find('.selectBox').fadeIn(100);
			$(this).blur();
		});
		$('.selectBox').on("click",".cancel",function(){
			$(this).parents(".selectBox").fadeOut(100)
		})
		$('.selectBox').on("click",".sure",function(){

			$(this).parents(".selectBox").fadeOut(100)
		})
		$('body').on("click",'.selectBox li',function(){
			var val = $(this).text();
			var sid = $(this).attr("sid");
			$(this).addClass("on").siblings("li").removeClass("on");
			$(this).parents(".selectBox_all").find(".selectBox_input").attr('value',val).attr("sid",sid).trigger("change");
			$(this).parents(".selectBox_all").find(".selectBox_input_val").val(sid);
			$(this).parents(".selectBox").fadeOut(100);
		})
		// 下拉框
		$('.author_input').focus(function(){
			$('.selectBox').fadeOut(50);
			$('.author-box').fadeIn(100);
			$(this).blur();
		});
		$('.author-box').on("click",".cancel",function(){
			$(".author-box").fadeOut(100)
		})
		$('.author-box').on("click",".sure",function(){
			$(".author-box").fadeOut(100)
		})
		$('.author-box .author-box-con-item').on("click",function(){
			var val = $(this).text();
			$(".author-box-con-item").removeClass("on");
			$(this).addClass("on");
			$(".author_input").val(val);
			;
			$(".author-box").fadeOut(100);

		})
		$('.author-box input[type="button"]').on("click",function(){
			var v = $(".author-search-val").val();
			if($("."+v).length == 0){
				$fb.fbNews({"type":"info","content":"没有'"+v+"'首字母的作者"})
			}
			$(".author-box-overflow").mCustomScrollbar("scrollTo","."+v);
		})
	})




//图片移出入
	$(function(){
		$('.obj_art').mouseenter(function(){
	$('.shade').addClass('show');
	});
	$('.obj_art').mouseleave(function(){

	  $('.shade').removeClass('show');
	})
	})

 /**/
