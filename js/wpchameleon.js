function setCookie(c_name,value,expiredays, path){
   var expiredate=new Date();
   expiredate.setDate(expiredate.getDate()+expiredays);
   var cookiestr=c_name+ "=" +escape(value)+
   ((expiredays==null) ? "" : ";expires="+expiredate.toGMTString())+";path="+path;
   document.cookie = cookiestr;
}

function getCookie(c_name){
   if (document.cookie.length>0){
     c_start=document.cookie.indexOf(c_name + "=");
      if (c_start!=-1){
         c_start=c_start + c_name.length+1;
         c_end=document.cookie.indexOf(";",c_start);
         if (c_end==-1) c_end=document.cookie.length;
         return unescape(document.cookie.substring(c_start,c_end));
      }
   }
   return "";
}

function WPChameleon_setVarient(varient, path){
	if(path=='') path = '/';
   setCookie("WPChameleon_varient",varient,100, path);
   window.location.reload();
}

function WPChameleon_clearNonRootC(wpurl){
	wpurl = wpurl + "/";
	var url = window.location.href;
	if(url!=wpurl){
		url = url.substring(window.location.hostname.length+window.location.protocol.length+2,url.length); // e.x. http://example.com/dir/dir/page/ would become /dir/dir/page/
		setCookie("WPChameleon_varient",1,-5, url); // delete the cookie
	}
}
