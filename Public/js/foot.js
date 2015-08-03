$(function(){
	// 生成随机验证码
	var verifyimg = $(".verifyimg").attr("src");
	$('.verifyimg').click(function(){
		if( verifyimg.indexOf('?')>0){
            $(".verifyimg").attr("src", verifyimg+'&random='+Math.random());
        }else{
            $(".verifyimg").attr("src", verifyimg.replace(/\?.*$/,'')+'?'+Math.random());
        }
	})
	
	//底部浮动
	var doc_height = $('body').height();
	var window_height = $(window).height();
	if(doc_height < window_height){
		$('footer').css('position', 'fixed');
	}
});