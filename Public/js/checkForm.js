// 检测身份证号
function checkIdcard(value, element, sexE, birthdayE) {
    value = trim(value);
    $(element).val(value);
    var area = {11: "北京", 12: "天津", 13: "河北", 14: "山西", 15: "内蒙古", 21: "辽宁", 22: "吉林", 23: "黑龙江", 31: "上海", 32: "江苏", 33: "浙江", 34: "安徽", 35: "福建", 36: "江西", 37: "山东", 41: "河南", 42: "湖北", 43: "湖南", 44: "广东", 45: "广西", 46: "海南", 50: "重庆", 51: "四川", 52: "贵州", 53: "云南", 54: "西藏", 61: "陕西", 62: "甘肃", 63: "青海", 64: "宁夏", 65: "xinjiang", 71: "台湾", 81: "香港", 82: "澳门", 91: "国外"};
    var idcard = value;
    var Y, JYM;
    var S, M;
    var sex_val;
    var idcard_array = new Array();
    idcard_array = idcard.split("");
    if (area[parseInt(idcard.substr(0, 2))] == null) {
        return false;
    }
    switch (idcard.length) {
        case 15:
            if ((parseInt(idcard.substr(6, 2)) + 1900) % 4 == 0 || ((parseInt(idcard.substr(6, 2)) + 1900) % 100 == 0 && (parseInt(idcard.substr(6, 2)) + 1900) % 4 == 0)) {
                ereg = /^[1-9][0-9]{5}[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|[1-2][0-9]))[0-9]{3}$/;
            } else {
                ereg = /^[1-9][0-9]{5}[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|1[0-9]|2[0-8]))[0-9]{3}$/;
            }
            if (ereg.test(idcard)) {
                
                if (parseInt(idcard_array[14]) % 2 == 0) {
                    sex_val = '2';
                } else {
                    sex_val = '1';
                }
                $('#psex').val(sex_val);
                $('#pbirth').val(idcard.substr(6, 4) + '-' + idcard.substr(10, 2) + '-' + idcard.substr(12, 2));
                /*
                if (sexE) {
                    //sexE.find('#sex_select').text(sex_val);
                    sexE.find('input[name^=person_sex]').each(function(){
                        if($(this).val()==sex_val){
                            $(this).attr('checked','checked');
							return false;
                        }
                    });
                }
                birthdayE && birthdayE.val('19' + idcard.substr(6, 2) + '-' + idcard.substr(8, 2) + '-' + idcard.substr(10, 2));
                birthdayE && birthdayE.next('b.wrongTips').remove();
                */
                return true;
            } else {
                return false;
            }
            break;
        case 18:
            if (parseInt(idcard.substr(6, 4)) % 4 == 0 || (parseInt(idcard.substr(6, 4)) % 100 == 0 && parseInt(idcard.substr(6, 4)) % 4 == 0)) {
                ereg = /^[1-9][0-9]{5}(19|20)[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|[1-2][0-9]))[0-9]{3}[0-9Xx]$/;
            } else {
                ereg = /^[1-9][0-9]{5}(19|20)[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|1[0-9]|2[0-8]))[0-9]{3}[0-9Xx]$/;
            }
            if (ereg.test(idcard)) {
                S = (parseInt(idcard_array[0]) + parseInt(idcard_array[10])) * 7 + (parseInt(idcard_array[1]) + parseInt(idcard_array[11])) * 9 + (parseInt(idcard_array[2]) + parseInt(idcard_array[12])) * 10 + (parseInt(idcard_array[3]) + parseInt(idcard_array[13])) * 5 + (parseInt(idcard_array[4]) + parseInt(idcard_array[14])) * 8 + (parseInt(idcard_array[5]) + parseInt(idcard_array[15])) * 4 + (parseInt(idcard_array[6]) + parseInt(idcard_array[16])) * 2 + parseInt(idcard_array[7]) * 1 + parseInt(idcard_array[8]) * 6 + parseInt(idcard_array[9]) * 3;
                Y = S % 11;
                M = "F";
                JYM = "10X98765432";
                M = JYM.substr(Y, 1);
                if (M == idcard_array[17]) {                   
                    if (parseInt(idcard_array[16]) % 2 == 0) {
                        sex_val = '2';
                    } else {
                        sex_val = '1';
                    }
                    $('#psex').val(sex_val);
                    $('#pbirth').val(idcard.substr(6, 4) + '-' + idcard.substr(10, 2) + '-' + idcard.substr(12, 2));
                    /*
                    if (sexE) {
                        //sexE.find('#sex_select').text(sex_val);
                        sexE.find('input[name^=person_sex]').each(function(){
                            if($(this).val()==sex_val){
                                $(this).attr('checked','checked');
								return false;
                            }
                        });
                    }
                    
                    birthdayE && birthdayE.val(idcard.substr(6, 4) + '-' + idcard.substr(10, 2) + '-' + idcard.substr(12, 2));
                    */

                    
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        default:
            return false;
            break;
    }
}

// 检查手机号
function checkMobile(value) {
    if (!(/^1[3|4|5|7|8][0-9]\d{8}$/.test(value))) {
        return false;
    }
    return true
}



function checkIdcardWap(value, element, sexE, birthdayE) {
    value = trim(value);
    $(element).val(value);
    /*
     var idLength = value.length;
     if(idLength != 15 && idLength != 18){
     return false;
     }
     for(i = 0; i < idLength; i++){
     var nm = value.charAt(i);
     if((nm < 0 || nm > 9) && idLength != 17 && idLength != 15){
     return false;
     }
     if(idLength == 17 && ((nm != 'X' && nm != 'x') && isNaN(nm) )){
     return false;
     }
     }
     return true;
     */
    var area = {11: "北京", 12: "天津", 13: "河北", 14: "山西", 15: "内蒙古", 21: "辽宁", 22: "吉林", 23: "黑龙江", 31: "上海", 32: "江苏", 33: "浙江", 34: "安徽", 35: "福建", 36: "江西", 37: "山东", 41: "河南", 42: "湖北", 43: "湖南", 44: "广东", 45: "广西", 46: "海南", 50: "重庆", 51: "四川", 52: "贵州", 53: "云南", 54: "西藏", 61: "陕西", 62: "甘肃", 63: "青海", 64: "宁夏", 65: "xinjiang", 71: "台湾", 81: "香港", 82: "澳门", 91: "国外"};
    var idcard = value;
    var Y, JYM;
    var S, M;
    var sex_val;
    var idcard_array = new Array();
    idcard_array = idcard.split("");
    if (area[parseInt(idcard.substr(0, 2))] == null) {
        return false;
    }
    switch (idcard.length) {
        case 15:
            if ((parseInt(idcard.substr(6, 2)) + 1900) % 4 == 0 || ((parseInt(idcard.substr(6, 2)) + 1900) % 100 == 0 && (parseInt(idcard.substr(6, 2)) + 1900) % 4 == 0)) {
                ereg = /^[1-9][0-9]{5}[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|[1-2][0-9]))[0-9]{3}$/;
            } else {
                ereg = /^[1-9][0-9]{5}[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|1[0-9]|2[0-8]))[0-9]{3}$/;
            }
            if (ereg.test(idcard)) {
                if (parseInt(idcard_array[14]) % 2 == 0) {
                    sex_val = '女';
                } else {
                    sex_val = '男';
                }
                if (sexE) {
                    sexE.children('s').each(function(){
                        if($(this).children().val()==sex_val){
                            $(this).children().attr("checked","checked");
                        }
                    });
                    sexE.next().val(sex_val);
                }
                birthdayE && birthdayE.val('19' + idcard.substr(6, 2) + '-' + idcard.substr(8, 2) + '-' + idcard.substr(10, 2));
                birthdayE && birthdayE.next('i.prompt').remove();
                return true;
            } else {
                return false;
            }
            break;
        case 18:
            if (parseInt(idcard.substr(6, 4)) % 4 == 0 || (parseInt(idcard.substr(6, 4)) % 100 == 0 && parseInt(idcard.substr(6, 4)) % 4 == 0)) {
                ereg = /^[1-9][0-9]{5}(19|20)[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|[1-2][0-9]))[0-9]{3}[0-9Xx]$/;
            } else {
                ereg = /^[1-9][0-9]{5}(19|20)[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|1[0-9]|2[0-8]))[0-9]{3}[0-9Xx]$/;
            }
            if (ereg.test(idcard)) {
                S = (parseInt(idcard_array[0]) + parseInt(idcard_array[10])) * 7 + (parseInt(idcard_array[1]) + parseInt(idcard_array[11])) * 9 + (parseInt(idcard_array[2]) + parseInt(idcard_array[12])) * 10 + (parseInt(idcard_array[3]) + parseInt(idcard_array[13])) * 5 + (parseInt(idcard_array[4]) + parseInt(idcard_array[14])) * 8 + (parseInt(idcard_array[5]) + parseInt(idcard_array[15])) * 4 + (parseInt(idcard_array[6]) + parseInt(idcard_array[16])) * 2 + parseInt(idcard_array[7]) * 1 + parseInt(idcard_array[8]) * 6 + parseInt(idcard_array[9]) * 3;
                Y = S % 11;
                M = "F";
                JYM = "10X98765432";
                M = JYM.substr(Y, 1);
                if (M == idcard_array[17]) {
                    if (parseInt(idcard_array[16]) % 2 == 0) {
                        sex_val = '女';
                    } else {
                        sex_val = '男';
                    }
                    if (sexE) {
                        sexE.children('s').each(function(){
                            if($(this).children().val()==sex_val){
                                $(this).children().attr("checked","checked");
                            }
                        });
                        sexE.next().val(sex_val);
                    }
                    birthdayE && birthdayE.val(idcard.substr(6, 4) + '-' + idcard.substr(10, 2) + '-' + idcard.substr(12, 2));
                    birthdayE && birthdayE.next('i.prompt').remove();
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        default:
            return false;
            break;
    }
}

function checkPassport(value) {
    if (!(/^[\w]+$/.test(value))) {
        return false;
    }
    return true;
}
//检查军官证
function checkOfficersCertificate(value) {
//  if(!(/^[\w]+$/.test(value))){
//      return false;
//  }
    return true;
}
//检查回乡证（港澳居民来往内地通行证）
//通行证证件号码共11位。第1位为字母，“H”字头签发给香港居民，“M”字头签发给澳门居民；第2位至第11位为数字，前8位数字为通行证持有人的终身号，后2位数字表示换证次数，首次发证为00，此后依次递增。
function checkHomeCar(value) {
    if (!(/^[HM][\d]{10}$/.test(value))) {
        return false;
    }
    return true;
}
//去左右空格;
function trim(s){
    return s.replace(/(^\s*)|(\s*$)/g, "");
}