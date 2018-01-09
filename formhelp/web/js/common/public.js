/*时间格式化*/
function formatDateTime() {    
    var date = new Date();  
    var y = date.getFullYear();    
    var m = date.getMonth() + 1;    
    m = m < 10 ? ('0' + m) : m;    
    var d = date.getDate();    
    d = d < 10 ? ('0' + d) : d;    
    var h = date.getHours();  
    h = h < 10 ? ('0' + h) : h;  
    var minute = date.getMinutes();  
    var second = date.getSeconds();  
    minute = minute < 10 ? ('0' + minute) : minute;    
    second = second < 10 ? ('0' + second) : second;   
    return y + '-' + m + '-' + d+' '+h+':'+minute+':'+second;    
};

/*显示浮动层*/
function manager_showMore(obj,id){
	 $(".tabsMoreList").hide();
	 objs_idname = $(obj).next();
	if(typeof(id)!='undefined')
		objs_idname = $("#"+id);
	if(objs_idname.css('display')=='none')
	{
		var bottom=$(obj).position().bottom;
		var left=$(obj).position().left;
		objs_idname.css("bottom",bottom);
		objs_idname.css("left",left);
		objs_idname.show();;
		showFlag = true;
		$("body").unbind("click", noneMore_list).bind("click", noneMore_list);
	 }
	 else noneMore_list();
}

/*隐藏浮动层*/
function noneMore_list()
{
	if(!showFlag && objs_idname.css('display')=="block"){
		objs_idname.hide();
		$("body").unbind("click", noneMore_list);
	}
	showFlag = false;
}
/*身份证校验*/
function validateIdCard(idCard){
 //15位和18位身份证号码的正则表达式
 	var regIdCard=/^(^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$)|(^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[Xx])$)$/;

 //如果通过该验证，说明身份证格式正确，但准确性还需计算
 	if(regIdCard.test(idCard)){
	  if(idCard.length==18){
		   var idCardWi=new Array( 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2 ); //将前17位加权因子保存在数组里
		   var idCardY=new Array( 1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2 ); //这是除以11后，可能产生的11位余数、验证码，也保存成数组
		   var idCardWiSum=0; //用来保存前17位各自乖以加权因子后的总和
		   for(var i=0;i<17;i++){
		    idCardWiSum+=idCard.substring(i,i+1)*idCardWi[i];
		   }
		   var idCardMod=idCardWiSum%11;//计算出校验码所在数组的位置
		   var idCardLast=idCard.substring(17);//得到最后一位身份证号码
		   //如果等于2，则说明校验码是10，身份证号码最后一位应该是X
		   if(idCardMod==2){
			    if(idCardLast=="X"||idCardLast=="x"){
					return true;
			    }else{
			    	layer.open({content: '身份证号码错误',skin: 'footer',time: 2 });
			     	return false;
			    }
		   }else{
		    //用计算出的验证码与最后一位身份证号码匹配，如果一致，说明通过，否则是无效的身份证号码
			    if(idCardLast==idCardY[idCardMod]){
					return true;
			    }else{
			    	layer.open({content: '身份证号码错误',skin: 'footer',time: 2 });
			     	return false;
			    }
		   }
	  	} 
	 }else{
	 	layer.open({content: '身份证格式不正确',skin: 'footer',time: 2 });
	  	return false;
	 }
}

/*校验密码*/
function validatePwd(passWord){
	var regExp = /^[0-9 | A-Z | a-z]{6,20}$/;
	if(!passWord.match(regExp)){
		return false;
	}else{
		return true;
	}
}

/*校验手机号码*/
function validatePhone(phoneNum){
	var phoneExp = /^0?1[0-9][0-9]\d{8}$/;
	if(!phoneNum.match(phoneExp)){
		return false;
	}else{
		return true;
	}
}

/*邮箱验证*/
function validateEmail(email){
	var emailExp = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if(!email.match(emailExp)){
		return false;
	}
	return true;
}

/*数字验证*/
function validateNum(number){
	var numberExp = /^\d+(\.\d+)?$/;
	if(!number.match(numberExp)){
		return false;
	}
	return true;
}

/*校验正整数*/
function validatePosNum(number){
	var numberExp = /^\+?[1-9][0-9]*$/;
	if(!number.match(numberExp)){
		return false;
	}
	return true;
}


/*校验邮政编码*/
function validatePostCode(postCode){
	var postCodeExp = /^[1-9][0-9]{5}$/;
	if(!postCode.match(postCodeExp)){
		return false;
	}
	return true;
}

function validateUserName(uname){
	var unameExp = /^[a-zA-Z]{1}([a-zA-Z0-9]|[_]){4,19}$/;
	if(!uname.match(unameExp)){
		return false;
	}
	return true;
}

/*身份证校验*/
function validateIdCard1(idCard){
 //15位和18位身份证号码的正则表达式
 	var regIdCard=/^(^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$)|(^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[Xx])$)$/;

 //如果通过该验证，说明身份证格式正确，但准确性还需计算
 	if(regIdCard.test(idCard)){
	  if(idCard.length==18){
		   var idCardWi=new Array( 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2 ); //将前17位加权因子保存在数组里
		   var idCardY=new Array( 1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2 ); //这是除以11后，可能产生的11位余数、验证码，也保存成数组
		   var idCardWiSum=0; //用来保存前17位各自乖以加权因子后的总和
		   for(var i=0;i<17;i++){
		    idCardWiSum+=idCard.substring(i,i+1)*idCardWi[i];
		   }
		   var idCardMod=idCardWiSum%11;//计算出校验码所在数组的位置
		   var idCardLast=idCard.substring(17);//得到最后一位身份证号码
		   //如果等于2，则说明校验码是10，身份证号码最后一位应该是X
		   if(idCardMod==2){
			    if(idCardLast=="X"||idCardLast=="x"){
					return true;
			    }else{
			    	layer.open({content: "身份证号码错误",btn: '我知道了'});
			     	return false;
			    }
		   }else{
		    //用计算出的验证码与最后一位身份证号码匹配，如果一致，说明通过，否则是无效的身份证号码
			    if(idCardLast==idCardY[idCardMod]){
					return true;
			    }else{
			    	layer.open({content: "身份证号码错误",btn: '我知道了'});
			     	return false;
			    }
		   }
	  	} 
	 }else{
	 	layer.open({content: "身份证格式不正确",btn: '我知道了'});
	  	return false;
	 }
}

/*合并数据列*/
function mergeCellsByField(tableID, colList, mainColIndex) {
    var ColArray = colList.split(",");//合并列参数（field）分割成数组模式
    var tTable = $('#' + tableID);//合并表ID
    var TableRowCnts = tTable.datagrid("getRows").length;//获取表长度
    var tmpA;//临时变量A
    var tmpB;//临时变量B
    var PerTxt = "";
    var CurTxt = "";//当前数据
    var alertStr = "";
    for (var i = 0; i <= TableRowCnts ; i++) {
        if(i == TableRowCnts){//
            CurTxt = "";
        }else{
            CurTxt = tTable.datagrid("getRows")[i][ColArray[mainColIndex]];//获取当前合并参数的列数据
        }
        if(PerTxt == CurTxt){
            tmpA += 1;
        }else{
            tmpB += tmpA;
            for(var j = 0; j < ColArray.length; j++){//遍历相同数据
                tTable.datagrid('mergeCells', {//合并列
                    index: i - tmpA,
                    field: ColArray[j],
                    rowspan: tmpA,
                    colspan: null
                });
            }
            tmpA = 1;
        }
        PerTxt = CurTxt;
    }
}

/*将代码值转换成对应的中文*/
function transCodeInfo(jsonInfo,codeIndex,codeVal){
	if(codeVal==""){
		return codeVal;
	}
	var result = "";
	for(var i=0; i<jsonInfo.length; i++){
		if(jsonInfo[i]['codeTypeID']==codeIndex && jsonInfo[i]['id']==codeVal){
			result = jsonInfo[i]['text'];
			break;
		}
	}
	return result;
}

/**
 * 检验是否为数字
 * @param {Object} val
 * @param {Object} len
 */
function xy_filedcheckcode_js_checkSelfFloat2(val,len,msg)
{
	if(val == ""){
		layer.msg(msg+'值不能为空',{icon: 5,anim:6});
		return false;
	}
	if(isNaN(val)||val.indexOf("e")!=-1||val.indexOf("E")!=-1)
	{
		layer.msg(msg+'只能输入数字',{icon: 5,anim:6});
		return false;
	}
	val=val.replace(/^\s*/, "").replace(/\s*$/, "");
	if(val==""){
		layer.msg(msg+'只能输入数字',{icon: 5,anim:6});
		return false;
	}
	var ss=val.substr(0,1);
	if(ss=="+")
	{
		layer.msg(msg+'不能出现+字符',{icon: 5,anim:6});
		return false;
	}
//	if(val.length>len)
//	{
//		layer.msg(msg+"输入的数字不能超过 "+len+" 位",{icon: 5,anim:6});
//		return false;
//	}
	if(parseFloat(val)<0){
		layer.msg(msg+"输入的数字不能小于0",{icon: 5,anim:6});
		return false;
	}
	var j=val.indexOf(".");
	if(j!=-1)
	{
		var m=val.substr(j+1,val.length);
		if(m.length>2)
		{
			layer.msg(msg+"小数点后超过了2个数字",{icon: 5,anim:6});
			return false;
		}
	}	
	return true;
}