/*用户登录*/
function checkLogin(form) {
    if(form.nickname.value=='') {
        alert("请输入昵称!");
        form.username.focus();
        return false;
    }
    if(form.password.value==''){
        alert("请输入登录密码!");
        form.password.focus();
        return false;
    }

    return true;
}
/*用户注册*/
function checkRegist(form) {
    if(form.nickname.value=='') {
        alert("请输入昵称!");
        form.nickname.focus();
        return false;
    }
    if(form.email.value==''){
        alert("请输入邮箱!");
        form.email.focus();
        var myReg=/^[a-zA-Z0-9_-]+@([a-zA-Z0-9]+\.)+(com|cn|net|org)$/;
        if(!myReg.test(form.email.value)){
            form.email.focus();
            alert('邮箱格式不正确')
            return false;
        }
        return false;
    }
    if(form.phone.value==''){
        alert("请输入手机号码!");
        form.phone.focus();
        return false;
    }
    if(form.password.value==''){
        alert("请输入登录密码!");
        form.password.focus();
        return false;
    }
    if(form.repassword.value==''){
        alert("请输入确认密码!");
        form.repassword.focus();
        return false;
    }else{
        if(form.password.value!=form.repassword.value){
            alert("两次密码不一致，请重新输入!");
            form.password.focus();
            return false;
        }
    }
    return true;
}

/**
 *添加记账信息验证
 */
function checkAddAccount(form) {
    if(form.small_type.value==''){
        alert("方向不能为空！");
        return false;
    }
    if(form.money.value==''){
        form.money.focus();
        alert("金额不能为空！");
        return false;
    }

    //在数据库中，3表示应收，4为应付
    if(form.big_type.value=='3' || form.big_type.value=='4') {
       if(form.enddate.value==''){
           form.end_time.focus();
           alert("截止时间不能为空！");
       }
        return false;
    }
    return true;
}

/**
 * 用户修改资料验证
 * 主要是校验用户密码的修改
 */

function checkUpdmyself(form) {
    if(form.oldpassword.value != ''){
        if(form.password.value == ''){
            form.password.focus();
            alert("新密码不能为空！");
            return false;
        }else {
            if(form.repassword.value == ''){
                form.repassword.focus();
                alert("确认密码不能为空！");
                return false;
            }else {
                if(form.password.value!=form.repassword.value){
                    alert("两次密码不一致，请重新输入!");
                    form.password.focus();
                    return false;
                }
            }
        }
    }
    return true;
}

/**
 * 用户密码重置
 */
function checkReset(form) {
    if(form.email.value==''){
        alert("请输入邮箱!");
        form.email.focus();
        return false;
    }
    if(form.code.value==''){
        alert("请输入验证码!");
        form.code.focus();
        return false;
    }
    if(form.password.value==''){
        alert("请输入登录密码!");
        form.password.focus();
        return false;
    }
    if(form.repassword.value==''){
        alert("请输入确认密码!");
        form.repassword.focus();
        return false;
    }else{
        if(form.password.value!=form.repassword.value){
            alert("两次密码不一致，请重新输入!");
            form.password.focus();
            return false;
        }
    }
    return true;
}
//获取短信验证码
function getPhoneCode(url){
    //$(".getcode").attr('disabled',true);设置按钮可以点击
    //$(".getcode").attr('disabled',false);
    var email=$('#resetinputEmail').val();
    var myReg=/^[a-zA-Z0-9_-]+@([a-zA-Z0-9]+\.)+(com|cn|net|org)$/;
    if(!myReg.test(email)){
        alert('邮箱格式不正确')
        return false;
    }
    if(email){
        $(".getcode").attr('disabled',true);
        var cutSec=120;
        var timer=window.setInterval(function () {
            cutSec=cutSec - 1;
            if(cutSec >0){
                $('.getcode').html(cutSec+'s');
            }else{
                $(".getcode").attr('disabled',false);
                $('.getcode').html('重新发送');
                clearInterval(timer);
                return ;
            }
        },1000);

        $.post(url,{email:email},function (result) {
            alert(result.msg);
        });
    }else
        alert('请输入邮箱');


}