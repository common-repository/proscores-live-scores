document.addEventListener("DOMContentLoaded", function() {
         
        // Add Color Picker to all inputs that have 'color-field' class
       
        window.pro_vars={};window.pro_cs={"lmt":{"ani":true,"text":true}}
        window.codeGen=new function() {
			this.keywords={
				"en": {
					"words":[
						{w:"livescore",s:1},{w:"live scores",s:1},{w:"livescores",s:1},{w:"live score",s:1}
					],
					"domain":"livescores.pro"
				},
				"tr":{
					"words":[
						{w:"iddaa",s:1},{w:"iddaa",s:1},{w:"iddaa",s:1},{w:"canlı skor",s:1,l:"/"},{w:"canlı skor",s:1,l:"/"},{w:"canlı skor",s:1,l:"/"},{w:"maç sonuçları",s:1,l:"/"},{w:"canlı maç sonuçları",s:1,l:"/"},{w:"iddaa sonuçları",s:1,l:"/"},
						{w:"basketbol sonuçları",s:2,l:"/?sports=80"},{w:"canlı basketbol sonuçları",s:2,l:"/?sports=80"},{w:"basketbol canlı skor",s:2,l:"/?sports=80"},{w:"basketbol iddaa sonuçları",s:2,l:"/?sports=80"},
						{w:"masa tenisi sonuçları",s:3,l:"/?sports=77"},{w:"masa tenisi canlı skor",s:3,l:"/?sports=77"},
						{w:"tenis sonuçları",s:5,l:"/?sports=81"},
						{w:"buz hokeyi sonuçları",s:6,l:"/?sports=79"},
					],
					"domain":"macskorlari.com"
				},
				"ru": {
					"words":[
						{w:"лайвскоре",s:1},{w:"ливескоре",s:1},
						,{w:"лайвскор",s:1},{w:"лайв скоре",s:1},
						,{w:"livescore на русском",s:1},{w:"ливе скоре",s:1},
						,{w:"livescore",s:1},{w:"ливескоре на русском",s:1},
					],
					"domain":"ru.livescores.pro"
				},
				"sw": {
					"words":[
						{w:"mechi za leo",s:1},{w:"matokeo ya mechi za leo",s:1},
						,{w:"mechi za leo live",s:1},{w:"livescores",s:1},
						,{w:"livescore",s:1},{w:"livescore cz",s:1},
						,{w:"matokeo mubashara",s:1},,{w:"matokeo ya mechi za jana",s:1,"l":"/jana"},
						,{w:"livescore cz",s:1},{w:"matokeo ya jana",s:1}
					],
					"domain":"sw.livescores.pro"
				},
				"el": {
					"words":[
						{w:"livescore",s:1},{w:"live scores",s:1},{w:"livescores",s:1},{w:"live score",s:1}
					],
					"domain":"el.livescores.pro"
				},
				"hi": {
					"words":[
						{w:"livescore",s:1},{w:"live scores",s:1},{w:"livescores",s:1},{w:"live score",s:1}
					],
					"domain":"hi.livescores.pro"
				},
			}
			this.getkw=function(v) {
				var key=codeGen.keywords[chosenLang]==undefined ? "en" : chosenLang
				var kws=codeGen.keywords[key]["words"].filter(function(a) {return a.s==v})
				var rtn=kws[Math.floor(Math.random() *kws.length)]
				window.domain=codeGen.keywords[key]["domain"]
				link_is=rtn.l || "/";
				return rtn;
			}
			this.dom=function() {
				return document.querySelector("#kodis")
			}
			this.gen=function(loadis) {
				var title_is=""//codeGen.getkw((pro_cs.sportId || 1)).w
				var kod_is='<'+''+'script type="text/javascript" src="https://widgets.proscores.app/njs/'+window.chosenLang+'/prolivewidget.js" async></'+''+'script><a href="https://'+(window.domain || 'livescores.pro')+''+(window.path || "/")+'" title="'+title_is+'">'+(window.domain || "livescores.pro")+'</a>'
				if (Object.keys(pro_vars).length!=0 || Object.keys(pro_cs).length) {
					var kod_is_s=""
					kod_is_s='<'+''+'script type="text/javascript">'
					if (Object.keys(pro_vars).length!=0) kod_is_s+='window.proVars='+JSON.stringify(pro_vars)+";\n"
					if (Object.keys(pro_cs).length!=0) kod_is_s+='window.proCs='+JSON.stringify(pro_cs)+";\n"
					kod_is_s+='</'+''+'script>'
					kod_is=kod_is_s+kod_is
				}
				//codeGen.dom().value=kod_is
				var vars_ups="if (window.addEventListener) {window.addEventListener('message', varUp, true);} function varUp(a) {document.getElementsByTagName('html')[0].style.setProperty('--pro_','a');console.log(a.data)} ";
				//vars_ups+="window.parent.postMessage({message: 'Hello world'}, '*');"
				var vars_ups="window.addEventListener('message', function(e) {try {eval(e.data);} catch(err) { console.log(err) } })"
				var i_kod='<html lang="'+window.chosenLang+'"><head><'+''+'script>'+vars_ups+';</'+''+'script><meta charset="utf-8"></head></head><body style="font-family:arial;">'+kod_is+'</body></html>'
				if(loadis)  document.querySelector("onizle").innerHTML='<iframe frameborder=0 id="onizleme" scrolling="no" style="background-color: transparent; border: none; opacity: 1;" src="data:text/html,' + encodeURIComponent(i_kod)+'" width=100% height=500></iframe>'
			}
			this.setvar=function(k,dom) {
				//if (dom.getAttribute("type")!="color") dom.parentNode.parentNode.parentNode.querySelector("isp").innerText=dom.parentNode.innerText
				pro_vars[k]=dom.value;
				codeGen.gen(false)
				document.getElementById('onizleme').contentWindow.postMessage('document.getElementsByTagName("html")[0].style.setProperty("--pro_'+k+'","'+dom.value+'")','*');
				if(k=="score_bg" || k=="score_bg2" || k=="live_clr") {
					document.getElementById('onizleme').contentWindow.postMessage('document.querySelector("pro_home").style="background:var(--pro_score_bg)";document.querySelector("pro_score").style="background:var(--pro_score_bg2)";document.querySelector(".promac").setAttribute(\'_s\',102);document.querySelector(".promac").setAttribute(\'_evs\',1);','*');
				}
				if(k.indexOf("inc_")!=-1 || k=="md_tt") {
					document.getElementById('onizleme').contentWindow.postMessage('proLib.details.sample()','*');
				}
			}
			this.setp=((dom) => {
				var uri;
				try  {
					uri=new URL(dom.value)
				} catch(er) {
					alert("Enter a valid URL")
					return;
				}
				if(uri.host.indexOf("livescores.pro")!=-1 || uri.host.indexOf("macskorlari.com")!=-1) {
					var ss=uri.pathname.split("/");
					var ican=ss[1]=="a" || ss[1]=="b";
					if(!ican || isNaN(ss[2])) {
						alert("Please enter exactly the league URL")
					} else  {
						window.path=uri.pathname;
						pro_cs["list"]="league";
						codeGen.gen(true)
					}
				} else {
					alert("This URL not belong to one of our sites. Visit livescores.pro")
				}
			})
			this.setg=function(k,dom) {var val_is=dom.value
				if (dom.getAttribute("type")=="checkbox") val_is=dom.checked
				else {
					//dom.parentNode.parentNode.parentNode.querySelector("isp").innerText=dom.parentNode.innerText
				}
				if(k=="list" && val_is.split(",")[0]=="league") {
					if(val_is=="league,custom") {
						document.querySelector("#customLeague").classList.toggle("hide",false)
						return
					}
					document.querySelector("#customLeague").classList.toggle("hide",true)
					var uri=new URL(val_is.split(",")[1])
					val_is="league"
					window.path=uri.pathname
					var ss=uri.pathname.split("/")
					//pro_cs["lid"]=ss[2];pro_cs["s"]=ss[1]
				} else {
					document.querySelector("#customLeague").classList.toggle("hide",true)
					//delete pro_cs["lid"];delete pro_cs["s"];
					window.path=""
				}
				if(k=="lmt") {
					val_is=JSON.parse(val_is)
					if(val_is.ani) {
						document.querySelector("#lmtTokenNeed").classList.toggle("hide",false)
					} else  {
						document.querySelector("#lmtTokenNeed").classList.toggle("hide",true)
					}
				}
				pro_cs[k]=val_is;
				codeGen.gen(true)
			}
			this.setl=function(dom) {
				window.lang=dom.value.split(",")[0];
				window.domain=dom.value.split(",")[1];
				window.chosenLang=dom.value.split(",")[2];
				codeGen.gen(true)
			}
			this.reset=function() {
				var ky=Object.keys(pro_vars);
				for(var i=0;i<ky.length;i++) {
					document.getElementById('onizleme').contentWindow.postMessage('document.getElementsByTagName("html")[0].style.removeProperty("--pro_'+ky[i]+'")','*');
				}
				pro_vars={};
				document.querySelectorAll("[var]").forEach(function(el) {
					//el.val(el.attr("def"));
					el.value=el.getAttribute("def")
					document.getElementById('onizleme').contentWindow.postMessage('document.getElementsByTagName("html")[0].style.setProperty("--pro_'+el.getAttribute("var")+'","'+el.getAttribute("def")+'")','*');
				})
				codeGen.gen(false)
			}
			this.setbulk=function(v) {
				codeGen.reset();
				var ky=Object.keys(v);
				for(var i=0;i<ky.length;i++) {
					pro_vars[ky[i]]=v[ky[i]];
					try{
						document.querySelector("[var='"+ky[i]+"']").value=v[ky[i]];
						document.getElementById('onizleme').contentWindow.postMessage('document.getElementsByTagName("html")[0].style.setProperty("--pro_'+ky[i]+'","'+v[ky[i]]+'")','*');
					}catch(e) {}
				}
				codeGen.gen(false)
			}
			this.eval=function(v) {
				eval(v.value)
			}
		}
		window.chosenLang=document.querySelector("select[name='proscores_settings_options[proscores_lang]']").value || "en";
		document.querySelectorAll("[var]").forEach(function(el) {
			el.setAttribute("def",el.value)
			pro_vars[el.getAttribute("var")]=el.value;
		})
		codeGen.gen(true);
		document.querySelector("select[name='proscores_settings_options[proscores_lang]']").addEventListener("change",ev => {
			window.chosenLang=document.querySelector("select[name='proscores_settings_options[proscores_lang]']").value || "en";
			codeGen.gen(true);
			codeGenST.gen(true);
		})

		window.premium=new function() {
			this.createinvoice=(async () => {
				//if(document.querySelector("#proscores_token").value!="") return
				var success=true;
				//document.querySelector(".createinvoice").innerHTML="Creating..."
				try {
					var _idomain=window.location.host;
				} catch(er) {
					//document.querySelector(".createinvoice").innerHTML="Create Invoice"
					//alert("Enter a valid domain with http(s)://")
					success=false
				}
				if(success) {
					var _iemail="wordpress-user@proscores.app"
					if(!premium.validateEmail(_iemail)) {
						success=false
						//document.querySelector(".createinvoice").innerHTML="Create Invoice"
						alert("Enter a valid email.")
					}
				}
				if(success) {
					//alert("Generating Invoice "+_idomain+", "+_iemail)
					premium.go(_idomain,_iemail)
				}
			})
			this.validateEmail = (email) => {
			  return email.match(
			    /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
			  );
			};
			this.cache={}
			this.go=((_idomain,_iemail) => {
				fetch('https://widgets.proscores.app/njs/en/createTokener', {
					headers: {
				        "Content-Type": "application/x-www-form-urlencoded"
				    },
				    method: 'POST',
				    body: "host="+_idomain+"&email="+_iemail
			  	}).then((response) => response.json())
			  	.then(r => {
			  		premium.cache=r;
			  		//document.querySelector(".createinvoice").innerHTML="Created!"
			  		var tknIs=r.id+'-'+r.token;
			  		window.pro_cs.token=tknIs;
			  		document.querySelector("#proscores_token_text").innerHTML="<span style='margin:4px 2px;'>"+tknIs+"</span>"
			  		document.querySelector("#proscores_token").value=tknIs
			  		//codeGen.gen(true)
			  		if(r.expires_at=="1970-01-01 00:00" && r.needPayment) {
			  			document.querySelector("#proscores_activate").innerHTML="<span class='install-now button' onclick='premium.activeWD()' style='margin:4px 2px;'>Activate</span><small style='margin:4px 2px;'><br>Activate token to use premium features.</small>"
			  		} else if (r.expires_at!="1970-01-01 00:00" && !r.needPayment) {
			  			document.querySelector("#proscores_activate").innerHTML="<small style='margin:4px 2px;'>Your token is active and will expire on "+r.expires_at+"</small>"
			  		}  else if (r.expires_at!="1970-01-01 00:00" && r.needPayment) {
			  			document.querySelector("#proscores_activate").innerHTML="<span class='install-now button' onclick='premium.activeWD()' style='margin:4px 2px;'>Activate Again</span><small style='margin:4px 2px;'><br>Your token expired on "+r.expires_at+"</small>"
			  		}
			  		var eks=""
			  		var tkn='<label for="tknl" style="margin-left:-10px;">Your Token</label><input type="text" id="tknl" disabled name="tknl" value="'+tknIs+'" var="token" style="margin-top:10px;margin-bottom:10px;" onchange=\'codeGen.setg("token",this)\' oninput=\'codeGen.setg("token",this)\'>'
			  		if(r.needPayment) {
			  			var iid=new URL(r.invoiceURL).searchParams.get("iid")
			  			eks="<ol><li>To complete the payment please visit: <a href='"+r.invoiceURL+"' target='_blank'>"+r.invoiceURL+"</a></li><li><a href='javascript:void(0)' onclick='premium.checkPayment("+iid+")' class='checkPayment'>Check Payment</a></li><li>For any inquiries, please contact us at <b>widgets@proscores.app</b></li></ol>"
			  		} else {
			  			eks="<ul><li>Your token is active and will expire on <b>"+r.expires_at+"</b></li></ul>"
			  		}
			  		//document.querySelector("#invoiceInfo").innerHTML="Your account created for http(s)://<b>"+r.domain+"</b>. "+tkn+" <div id='payInfo' class='karla fs12 pad10'></div>"+eks
			  	})
			})
			this.activeWD=(() => {
				const hm=["<style>#propayInfo:empty {display:none;}</style>"];
				hm.push("<ol style='font-size:12px'>")
					hm.push("<li>The token above is assigned to your domain, <b>"+premium.cache.domain+"</b>. If this is not your actual domain, do not proceed with activation until you have the correct domain.</li>")
					hm.push("<li>The pricing is <b>$200 per year</b>, payable in any cryptocurrency. Click the 'Payment' button below, select your preferred cryptocurrency, and complete the payment.</li>")
					hm.push("<li><a href='"+premium.cache.invoiceURL+"' target='_blank' class='install-now button' style='margin:4px 2px;'>Payment</a></li>")
					hm.push("<li>Once you send the cryptocurrency and all confirmations are complete, click the 'Check Payment' button.</li>")
					hm.push("<li><span onclick=premium.checkPayment() id=procheckbut class='install-now button' style='margin:4px 2px;'>Check Payment</span></li>")
				hm.push("</ol>")
				hm.push("<div id=propayInfo style='font-size: 12px; padding: 6px; background: beige; border-radius: 4px; border: solid 0.5px red;'></div>")
				document.querySelector("#proscores_activate").innerHTML=hm.join("")
			})
			this.checkPayment=(() => {
				var iid=new URL(premium.cache.invoiceURL).searchParams.get("iid")
				document.querySelector("#propayInfo").innerHTML="";
				document.querySelector("#procheckbut").innerHTML="Checking.."
				fetch('https://widgets.proscores.app/njs/en/checkPayments', {
					headers: {
				        "Content-Type": "application/x-www-form-urlencoded"
				    },
				    method: 'POST',
				    body: "iid="+iid
			  	}).then((response) => response.json())
			  	.then(r => {
			  		if(r.data.status=="granted") {
			  			premium.createinvoice();
			  		} else if (r.data.status=="waiting") {
			  			document.querySelector("#procheckbut").innerHTML="Check Payment Again"
			  			document.querySelector("#propayInfo").innerHTML=("If you have already sent the cryptocurrency, please wait for all confirmations to be processed. Once done, click the 'Check Payment' button again.")
			  		} else if(r.data.status=="empty") {
			  			document.querySelector("#procheckbut").innerHTML="Check Payment Again"
			  			document.querySelector("#propayInfo").innerHTML=("Please click the 'Payment' button above, choose the cryptocurrency you would like to use for payment, and complete the transaction. Once done, click the 'Check Payment' button again.")
			  		}
			  	})
			})
		}
		premium.createinvoice();

		window.pro_varsST={}
		window.codeGenST=new function() {
			this.gen=function(loadis) {
				var title_is=""//codeGen.getkw((pro_cs.sportId || 1)).w
				var kod_is=''
				if (Object.keys(pro_varsST).length!=0) {
					var kod_is_s=""
					kod_is_s='<'+''+'script type="text/javascript">'
					if (Object.keys(pro_varsST).length!=0) kod_is_s+='window.proStandingsStyle='+JSON.stringify(pro_varsST)+";\n"
					kod_is_s+='</'+''+'script>'
					kod_is=kod_is_s+kod_is
				}
				kod_is+='<a href="https://'+(window.domain || 'livescores.pro')+''+(window.path || "/a/50/league/england-premier-league/standings")+'" >'+(window.domain || "livescores.pro")+'</a><'+''+'script type="text/javascript" src="https://widgets.proscores.app/njs/'+(window.chosenLang || 'en')+'/prowidgetdeep.js?ver=1.0" async onload="proDeepWidgets();"></'+''+'script>'
				//codeGen.dom().value=kod_is
				var vars_ups="if (window.addEventListener) {window.addEventListener('message', varUp, true);} function varUp(a) {document.getElementsByTagName('html')[0].style.setProperty('--prostandings_','a');console.log(a.data)} ";
				//vars_ups+="window.parent.postMessage({message: 'Hello world'}, '*');"
				var vars_ups="window.addEventListener('message', function(e) {try {eval(e.data);} catch(err) { console.log(err) } })"
				var i_kod='<html lang="'+window.chosenLang+'"><head><'+''+'script>'+vars_ups+';</'+''+'script><meta charset="utf-8"></head></head><body style="font-family:arial;">'+kod_is+'</body></html>'
				if(loadis)  document.querySelector("onizle2").innerHTML='<iframe frameborder=0 id="onizlemeST" scrolling="no" style="background-color: transparent; border: none; opacity: 1;" src="data:text/html,' + encodeURIComponent(i_kod)+'" width=100% height=500></iframe>'
			}
			this.setvar=function(k,dom) {
				//if (dom.getAttribute("type")!="color") dom.parentNode.parentNode.parentNode.querySelector("isp").innerText=dom.parentNode.innerText
				pro_varsST[k]=dom.value;
				codeGenST.gen(false)
				document.getElementById('onizlemeST').contentWindow.postMessage('document.getElementsByTagName("html")[0].style.setProperty("--prostandings_'+k+'","'+dom.value+'")','*');
			}
		}		
		document.querySelectorAll("[var]").forEach(function(el) {
			el.setAttribute("def",el.value)
			pro_varsST[el.getAttribute("var")]=el.value;
		})
		codeGenST.gen(true);
})