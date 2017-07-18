$(function(){
	$(".user").hover(function(){
		$(this).find(".user-box").slideDown(300)
	},function(){
		$(this).find(".user-box").stop().slideUp(300)
	});
	//登录

	$("#login").on("submit",function(){
		var mobile = $('[ name="mobile"]').val();
		var password = $('[ name="password"]').val();
		var remember = 0;
		if($('[ name="remember"]').attr('checked')){
			var remember = 1;
		}
		if(!fbUi.expPhone(mobile)){
			fbUi.fbNews({"type":"warning","content":"请填写正确的手机号码"});
			$('[ name="mobile"]').addClass("input-error");
			return false;
		}
		if(password.length < 6){
			fbUi.fbNews({"type":"warning","content":"密码不能少于六位数"});
			$('[ name="password"]').addClass("input-error");
			return false;
		}
		$.ajax({
			type: "POST",
			url: "user.php?act=ajax_act_login",
			data: {mobile:mobile,password:password,remember:remember},
			dataType: "json",
			success: function(data){
				if(data.error == 0)
				{
					window.location.href="user.php";
				}else{
					$fb.fbNews({"content":data.message,"type":"warning"})
				}
			 }
		});
		return false;
	});

	//注册
	//
	$("#reg").on("submit",function(){
		var username = $("input[name='username']").val();
		var password = $("input[name='pass']").val();
		var confirm_password = $("input[name='repass']").val();
		var mobile = $("input[name='mobile']").val();
		var code = $("input[name='code']").val();

		if(!fbUi.expPhone(mobile)){
			$('[ name="mobile"]').addClass("input-error");
		}
		if(!fbUi.expEmpty(code)){
			$('[ name="code"]').addClass("input-error");
		}
		if(password.length < 6){
			$('[ name="pass"]').addClass("input-error");
		}
		if(password != confirm_password){
			$('[ name="pass"]').addClass("input-error");
			$('[ name="repass"]').addClass("input-error");
		}
		if(!fbUi.expEmpty(username)){
			$('[ name="username"]').addClass("input-error");
		}
		if(!$('[name="agreement"]').is(':checked')){
			fbUi.fbNews({"type":"warning","content":"请阅读并同意《 天佑用户协议 》"});
			return false;
		}
		$.ajax({
			type: "POST",
			url: "user.php?act=act_register",
			data: {username:username,password:password,confirm_password:confirm_password,mobile:mobile,agreement:1,mobile_code:code},
			dataType: "json",
			success: function(data){
				if(data.error == 0)
				{
					window.location.href="user.php";
				}else{
					$fb.fbNews({"content":data.message,"type":"warning"})
				}
			 }
		});
		return false;
	});
	//验证手机号码
	$('[ name="mobile"]').on("blur",function(){
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
	$('[ name="username"]').on("blur",function(){
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
		var tell = $("[name='mobile']").val();
		if(!$fb.expPhone(tell)){
			$fb.fbNews({"type":"danger","content":"手机号码格式错误"});
			return false;
		}
		if($(this).hasClass("active")){
			return false;
		}
		$(this).addClass("active")
		var t = 60;
		fbUi.getCode("user.php?act=send_mobile_code&mobile="+tell,function(){$(".getCode").html("60秒后可重新获取");});
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
	$(".worksOperation input").on("change",function(){
		for(var i = 0,c=$(".worksOperation input").length ; i < c ; i++){
			if($(".worksOperation input").eq(i).is(":checked")){
				$(".worksOperation label").eq(i).addClass("active");
			}else{
				$(".worksOperation label").eq(i).removeClass("active");
			}
		}
	})
	//艺术品收藏
	/*
	$(".worksCollection").on("click",function(){
		$.getJSON('test.json', function(data){
		   $(this).toggleClass("active");
		});
	})*/
	worksCollection = function(goodsId){
		$.getJSON('user.php?act=collect&id='+goodsId, function(data){
			if(data.error == 0)
			{	if(data.active == 1)
				{
					$(".collect").toggleClass("active");
				}else{
					$(".collect").removeClass("active");
				}
			}else{
				$fb.fbNews({"content":data.message,"type":"warning"});
			}
		});
	};

	/*
	//删除购物车
	$(".cart .delete").on("click",function(){
		$fb.showModal({
		    title:"提示",
		    content:"是否删除该商品？"
		},function(){
			$.ajax({
				type: "POST",
				url:'flow.php?act=ajax_drop_goods',
				data:{'ajax':1},
				error: function(request) {
					alert("连接失败");
				},
				success: function(data) {
					console.log(data.message);
					if(data.error ==1)
					{
						$fb.fbNews({"content":"删除成功","type":"warning"})
					}
					else if(data.error == 0)
					{
						$(".address-item_"+id).remove();
						$fb.fbNews({"content":"删除成功","type":"success"})
					}
				}
			});
		});
	})
*/
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
