// 剪切图片
function jcropPop(img_url, url) {
	var ori = $('#jcrop-pic');
	var pre = $('#jcrop-preview');
	ori.attr('src', img_url);
	pre.attr('src', img_url);
	//jcrop
	var jcrop_api,
		boundx,
		boundy,
		xbase = 100,
		ybase = 100,
		xsize = 100,
		ysize = 100;
	//init
	initJcrop();
	//初始化
	function initJcrop(){
		$('.requiresjcrop').hide();
		$('#jcrop-pic').Jcrop({
			onChange: updatePreview,
			onSelect: updatePreview,
			//minSize: [xsize, ysize],
			aspectRatio: xsize / ysize
			
		},function(){
			var bounds = this.getBounds();
			boundx = bounds[0];
			boundy = bounds[1];
			jcrop_api = this;
			jcrop_api.animateTo([xbase, ybase, xbase + xsize, ybase + ysize]);
			$('.requiresjcrop').show();
		});
	};
	//预览
	function updatePreview(c){
		if(parseInt(c.w) > 0){
			var rx = xsize / c.w;
			var ry = ysize / c.h;
			$('#jcrop-preview').css({
				width: Math.round(rx * boundx) + 'px',
				height: Math.round(ry * boundy) + 'px',
				marginLeft: '-' + Math.round(rx * c.x) + 'px',
				marginTop: '-' + Math.round(ry * c.y) + 'px'
			});
		}
		updateCoords(c);
	}
	//上传数据
	function updateCoords(c){
		$('#preview-form .x').val(c.x);
		$('#preview-form .y').val(c.y);
		$('#preview-form .w').val(c.w);
		$('#preview-form .h').val(c.h);
	};
	//show pop
	$('.avatarpop_mask, .avatarpop_box').fadeIn();
	
	//close
	$('.avatarpop_box .btn_cancel').on('click', function () {
		$('#photoToUpload').val('');
		jcrop_api.destroy();
		$('#jcrop-pic, #jcrop-preview').attr('src', '');
		$('.avatarpop_box, .avatarpop_mask').fadeOut();
	});
	//剪切
	$('#Crop_Image').unbind().bind('click', function(){
		if(parseInt($('#preview-form .w').val())){
			$.ajax({
				url: url,
				type: 'post',
				data: { 'url': img_url, 'x': $('#preview-form .x').val(), 'y': $('#preview-form .y').val(), 'w': $('#preview-form .w').val(), 'h': $('#preview-form .h').val()},
				dataType: 'json',
				success: function(result){
					if(result.code == 1){
						$('#show_photo').attr('src', result.data + '?' + (new Date().getTime()));
						$('input[name=headimg]').val(result.data);
						if($('.nav_uinfo').find('.nav_headimg').length > 0){
							$('.nav_uinfo').find('.nav_headimg').attr('src', result.data + '?' + (new Date().getTime()));
						}
						$('#photoToUpload').val('');
						jcrop_api.destroy();
						$('#jcrop-pic, #jcrop-preview').attr('src', '');
						$('.avatarpop_box, .avatarpop_mask').fadeOut();
					}else{
						alert(result.msg);
						return false;
					}
				}
			});
		}else{
			alert('Please select a crop region then press submit.');
			return false;
		}
	});
};

//将form中的值转换为键值对。
function getFormJson(frm) {
    var o = {};
    var a = $(frm).serializeArray();
    $.each(a, function () {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });

    return o;
}

//获取某年某月有几天
function getday(m, y, url){
	$.post(url, {y: y, m: m}, function(data){
		if(data.error == 1){
			alert(data.errormsg);
		}else{
			$('select[name=day]').empty().append(data.con);
		}
	}, 'json');
}

//获取城市 
function getcity(p, url){
	$.post(url, {p: p}, function(data){
		data = eval("("+data+")");
		if(data.error == 1){
			alert(data.errormsg);
		}else{
			if(typeof(data.con)!='undefined' && data.con.length>1){
				var clist = data.con;
				var html = '<option value="0">请选择城市</option>';
	            for(i=0; i<clist.length; i++){
	                html += '<option value="'+clist[i].no+'">'+clist[i].areaname+'</option>';
	            }
				$('select[name=city]').empty().append(html);
				$('select[name=city]').css('display', 'inline-block');
			}
		}
	})
}

//关注，取消关注
function follow_toggle(url, fid, flag){
	$.post(url, {fid:fid},function(data){
		if(data.error == 1){
			tcc(data.errormsg);
		}else{
			if(flag == 1){
				$('.dofollow').removeClass('btn-success');
				$('.dofollow').addClass('btn-default');
				$('.dofollow').attr('event-node','unfollow');
				$('.dofollow').html('<span class="glyphicon glyphicon-ok" aria-hidden="true" data-id="<{$res[uid]}>" ></span> 已关注'); 
			}else{
				$('.dofollow').removeClass('btn-default');
				$('.dofollow').addClass('btn-success');
				$('.dofollow').attr('event-node','dofollow');
				$('.dofollow').html('<span class="glyphicon glyphicon-plus" aria-hidden="true" data-id="<{$res[uid]}>" ></span> 关注此人');
			}
		}
	}, 'json');
}

//删除评论
function del_comment(url, cid, eid){
	$.post(url, {cid:cid}, function(data){
		if(data.error == 1){
			tcc(data.errormsg);
		}else{
			$(eid).remove();
		}
	}, 'json');
}

//弹出框
function tcc(msg){
	alert(msg);
}

//活动报名时，增加报名人
function addCheckPeople(obj){
    var len = $(".order-member-list input:checkbox:checked").length; 
    var con = '';
    var i = 1;
    if(len>3){
        tcc('每个账户，同一个活动，最多只能添加3个人');
        $(obj).attr('checked',false);
        return false;
    } else if (len == 0){
        con = '<div class="form-field tour-info " style="z-index:100" ><div class="tour-head"><span class="tour-index">第1位出行驴友</span></div><div class="tour-list-input"><div class="form-group"><label for="qt_name_1" class="col-sm-2 control-label">姓名：</label><div class="col-sm-6"><input class="form-control" type="text" name="person_name_1" id="qt_name_1" value="" readonly></div><div class="col-sm-4"></div></div><div class="form-group"><label for="qt_phone_1" class="col-sm-2 control-label">手机：</label><div class="col-sm-6"><input class="form-control" type="text" name="person_phone_1" id="qt_phone_1" readonly value=""></div><div class="col-sm-4"></div></div><div class="form-group"><label for="" class="col-sm-2 control-label">证件：</label><div class="col-sm-2" style="padding-right:0px"><select class="form-control" name="qt_credentials_type_1" disabled><option value="1">身份证</option></select></div><div class="col-sm-4" style="padding-left: 5px;"><input class="form-control" type="text" name="credentials_no_1" id="qt_no_1" value="" readonly></div><div class="col-sm-4"></div></div></div></div>';
    } else {
        $(".order-member-list input:checkbox:checked").each(function(){
            var msg = $(this).attr('data');
            var msgArr = msg.split('-');
            con += '<div class="form-field tour-info " style="z-index:100" ><div class="tour-head"><span class="tour-index">第'+i+'位出行驴友</span></div><div class="tour-list-input"><div class="form-group"><label for="qt_name_1" class="col-sm-2 control-label">姓名：</label><div class="col-sm-6"><input class="form-control" type="text" name="person_name_1" id="qt_name_1" value="'+msgArr[0]+'" readonly></div><div class="col-sm-4"></div></div><div class="form-group"><label for="qt_phone_1" class="col-sm-2 control-label">手机：</label><div class="col-sm-6"><input class="form-control" type="text" name="person_phone_1" id="qt_phone_1" readonly value="'+msgArr[2]+'"></div><div class="col-sm-4"></div></div><div class="form-group"><label for="" class="col-sm-2 control-label">证件：</label><div class="col-sm-2" style="padding-right:0px"><select class="form-control" name="qt_credentials_type_1" disabled><option value="1">身份证</option></select></div><div class="col-sm-4" style="padding-left: 5px;"><input class="form-control" type="text" name="credentials_no_1" id="qt_no_1" value="'+msgArr[1]+'" readonly></div><div class="col-sm-4"></div></div></div></div>';
            i++;
        })
    }
    
    $('.order-member-list-info').html(con);
}
