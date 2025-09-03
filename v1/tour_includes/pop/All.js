var tmp_filename;
function BookmarkSite()
{
    var url = "http://www.musicianmatch.com/";
    var title = "Musician Match.com";
    
    if (document.all)
        window.external.AddFavorite(url, title);
    else if (window.sidebar)
        window.sidebar.addPanel(title, url, "")
}

function confirmbox(filename, height, width)
{ 
	tmp_filename = filename;
	
    if (__STINYBOX == 1)
    { 

        TINY.box.show(filename, 1, height, width, 1);
    }
    else
    { 
        newwindow = window.open(filename, 'name', 'height=460, width=600');
        if (window.focus) {newwindow.focus()}
        return false;
    }
}

function SignUp()
{
    window.location = "SignUpInfo.aspx";
}

function Delay(seconds)
{
    var buttonSend = document.getElementById("ctl00_ContentPlaceHolder1_FormViewUserProfile_ButtonSend");
    buttonSend.value = seconds;
    seconds--;
    if (seconds > 0)
    {
        setTimeout("Delay(" + seconds + ")", 1000);
    }
    else
    {
        var textBoxMessage = document.getElementById("ctl00_ContentPlaceHolder1_FormViewUserProfile_TextBoxMessage");
        textBoxMessage.value = "";
        textBoxMessage.disabled = false;
        buttonSend.value = "Send";
        buttonSend.disabled = false;
        
        var tableCaptcha = document.getElementById("ctl00_ContentPlaceHolder1_FormViewUserProfile_tablecaptcha");
        tableCaptcha.style.visibility = "visible";
    }
}

function Count(text, maxlength)
{
    if (text.value.length > maxlength)
    {
         text.value = text.value.substring(0, maxlength);
         alert("Max length is " + maxlength + " chars");
    }
}

function Send(button)
{
    button.value = "Sending...";
    return true;
    
}

function Upload(button)
{
    button.value = "Uploading...";
    return true;
}

function Search(button)
{
    button.value = "Searching...";
    return true;
}

function Save(button)
{
    if (typeof(Page_ClientValidate) == 'function')
    {
        if (Page_ClientValidate() == true)
        {
            button.value = "Saving...";
        }
        else
        {
            return true;
        }
    }
    else
    {
        button.value = "Saving...";
        return true;
    }
}

function AutoMatch()
{
    var GenderIdDropDownList = document.getElementById("ctl00_ContentPlaceHolder1_FormViewUserProfile_GenderIdDropDownList");
    var GenderForMeetDropDownList = document.getElementById("ctl00_ContentPlaceHolder1_FormViewUserProfile_GenderForMeetDropDownList");
    
    if (GenderIdDropDownList.selectedIndex == 1) GenderForMeetDropDownList.selectedIndex = 2;
    else if (GenderIdDropDownList.selectedIndex == 2) GenderForMeetDropDownList.selectedIndex = 1;
}

function WatermarkFocus(txtElem, strWatermark)
{
    if (txtElem.value == strWatermark) txtElem.value = '';
}

function WatermarkBlur(txtElem, strWatermark)
{
    if (txtElem.value == '') txtElem.value = strWatermark
}

function MutexCheckBox(cb)
{
    for (i = 0; i < cb.parentNode.childNodes.length; i++)
    {
        if (cb.parentNode.childNodes[i].name != null && cb.parentNode.childNodes[i].name.indexOf("CheckBox") != -1)
        {
            if (cb.parentNode.childNodes[i] != cb) cb.parentNode.childNodes[i].checked = false;
        }
    }
}

var TINY = {}; function T$(i) { return document.getElementById(i) } function T$$(e, p) { return p.getElementsByTagName(e) } TINY.accordion = function() { function slider(n) { this.n = n; this.h = []; this.c = [] } slider.prototype.init = function(t, e, m, o, k) { var a = T$(t), i = x = 0; this.s = k || '', w = [], n = a.childNodes, l = n.length; this.m = m || false; for (i; i < l; i++) { if (n[i].nodeType != 3) { w[x] = n[i]; x++ } } this.l = x; for (i = 0; i < this.l; i++) { var v = w[i]; this.h[i] = h = T$$(e, v)[0]; this.c[i] = c = T$$('div', v)[0]; h.onclick = new Function(this.n + '.pr(false,this)'); if (o == i) { h.className = this.s; c.style.height = 'auto'; c.d = 1 } else { c.style.height = 0; c.d = -1 } } }; slider.prototype.pr = function(f, d) { for (var i = 0; i < this.l; i++) { var h = this.h[i], c = this.c[i], k = c.style.height; k = k == 'auto' ? 1 : parseInt(k); clearInterval(c.t); if ((k != 1 && c.d == -1) && (f == 1 || h == d)) { c.style.height = ''; c.m = c.offsetHeight; c.style.height = k + 'px'; c.d = 1; h.className = this.s; su(c, 1) } else if (k > 0 && (f == -1 || this.m || h == d)) { c.d = -1; h.className = ''; su(c, -1) } } }; function su(c) { c.t = setInterval(function() { sl(c) }, 10) }; function sl(c) { var h = c.offsetHeight, d = c.d == 1 ? c.m - h : h; c.style.height = h + (Math.ceil(d / 10) * c.d) + 'px'; c.style.opacity = h / c.m; c.style.filter = 'alpha(opacity=' + h * 100 / c.m + ')'; if ((c.d == 1 && h >= c.m) || (c.d != 1 && h == 1)) { if (c.d == 1) { c.style.height = 'auto' } clearInterval(c.t) } }; return { slider: slider} } ();

TINY.box = function() {
    var p, m, b, fn, ic, iu, iw, ih, ia, f = 0;
    return {
        show: function(c, u, w, h, a, t) {
            if (!f) {
                p = document.createElement('div'); p.id = 'tinybox';
                m = document.createElement('div'); m.id = 'tinymask';
                b = document.createElement('div'); b.id = 'tinycontent';
                document.body.appendChild(m); document.body.appendChild(p); p.appendChild(b);
                m.onclick = TINY.box.hide; window.onresize = TINY.box.resize; f = 1
            }
            if (!a && !u) {
                p.style.width = w ? w + 'px' : 'auto'; p.style.height = h ? h + 'px' : 'auto';
                p.style.backgroundImage = 'none'; b.innerHTML = c
            } else {
                b.style.display = 'none'; p.style.width = p.style.height = '100px'
            }
            this.mask();
            ic = c; iu = u; iw = w; ih = h; ia = a; this.alpha(m, 1, 15, 1); /* ..., alpha, fade speed) background */
            if (t) { setTimeout(function() { TINY.box.hide() }, 1000 * t) }
        },
        fill: function(c, u, w, h, a) {
            if (u) {
                p.style.backgroundImage = '';
                var x = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                x.onreadystatechange = function() {
                    if (x.readyState == 4 && x.status == 200) {
						TINY.box.psh(x.responseText, w, h, a);
					}
                };
                x.open('GET', c, 1); x.send(null)
            } else {
                this.psh(c, w, h, a)
            }
        },
        psh: function(c, w, h, a) {
            if (a) {
                if (!w || !h) {
                    var x = p.style.width, y = p.style.height; b.innerHTML = c;
                    p.style.width = w ? w + 'px' : ''; p.style.height = h ? h + 'px' : '';
                    b.style.display = '';
                    w = parseInt(b.offsetWidth); h = parseInt(b.offsetHeight);
                    b.style.display = 'none'; p.style.width = x; p.style.height = y;
                } else {
                    b.innerHTML = c
					
					if(tmp_filename=='weather_curl.php'){
						// to call script tags in Popup open this comment.
						var sc_tags = b.getElementsByTagName("script");
						var sc_len = sc_tags.length;
						for(var sc_i=0; sc_i<sc_len; sc_i++){
							var new_sc = document.createElement("script");
							new_sc.text = sc_tags[sc_i].text;
							b.appendChild(new_sc);
						}
						//alert();
					}
                }
                this.size(p, w, h, 2) /* ..., resize speed) box */
            } else {
                p.style.backgroundImage = 'none'
            }
        },
        hide: function() {
            TINY.box.alpha(p, -1, 0, 1) /* ..., alpha, fade speed) box */
        },
        resize: function() {
            TINY.box.pos(); TINY.box.mask()
        },
        mask: function() {
            m.style.height = TINY.page.theight() + 'px';
            m.style.width = ''; m.style.width = TINY.page.twidth() + 'px'
        },
        pos: function() {
            var t = (TINY.page.height() / 2) - (p.offsetHeight / 2); t = t < 10 ? 10 : t;
            p.style.top = (t + TINY.page.top()) + 'px';
            p.style.left = (TINY.page.width() / 2) - (p.offsetWidth / 2) + 'px'
        },
        alpha: function(e, d, a, s) {
            clearInterval(e.ai);
            if (d == 1) {
                e.style.opacity = 0; e.style.filter = 'alpha(opacity=0)';
                e.style.display = 'block'; this.pos()
            }
            e.ai = setInterval(function() { TINY.box.twalpha(e, a, d, s) }, 1) /* ..., fade speed) box */
        },
        twalpha: function(e, a, d, s) {
            var o = Math.round(e.style.opacity * 100);
            if (o == a) {
                clearInterval(e.ai);
                if (d == -1) {
                    e.style.display = 'none';
                    e == p ? TINY.box.alpha(m, -1, 0, 1) : b.innerHTML = p.style.backgroundImage = '' /* ..., fade speed) background */
                } else {
                    e == m ? this.alpha(p, 1, 100, 1) : TINY.box.fill(ic, iu, iw, ih, ia) /* ..., fade speed) box */
                }
            } else {
                var n = o + Math.ceil(Math.abs(a - o) / s) * d;
                e.style.opacity = n / 100; e.style.filter = 'alpha(opacity=' + n + ')'
            }
        },
        size: function(e, w, h, s) {
            e = typeof e == 'object' ? e : T$(e); clearInterval(e.si);
            var ow = e.offsetWidth, oh = e.offsetHeight,
			wo = ow - parseInt(e.style.width), ho = oh - parseInt(e.style.height);
            var wd = ow - wo > w ? -1 : 1, hd = (oh - ho > h) ? -1 : 1;
            e.si = setInterval(function() { TINY.box.twsize(e, w, wo, wd, h, ho, hd, s) }, 1) /* ..., resize speed) box */
        },
        twsize: function(e, w, wo, wd, h, ho, hd, s) {
            var ow = e.offsetWidth - wo, oh = e.offsetHeight - ho;
            if (ow == w && oh == h) {
                clearInterval(e.si); p.style.backgroundImage = 'none'; b.style.display = 'block'
            } else {
                if (ow != w) { e.style.width = ow + (Math.ceil(Math.abs(w - ow) / s) * wd) + 'px' }
                if (oh != h) { e.style.height = oh + (Math.ceil(Math.abs(h - oh) / s) * hd) + 'px' }
                this.pos()
            }
        }
    }
} ();

TINY.page = function() {
    return {
        top: function() { return document.body.scrollTop || document.documentElement.scrollTop },
        width: function() { return self.innerWidth || document.documentElement.clientWidth },
        height: function() { return self.innerHeight || document.documentElement.clientHeight },
        theight: function() {
            var d = document, b = d.body, e = d.documentElement;
            return Math.max(Math.max(b.scrollHeight, e.scrollHeight), Math.max(b.clientHeight, e.clientHeight))
        },
        twidth: function() {
            var d = document, b = d.body, e = d.documentElement;
            return Math.max(Math.max(b.scrollWidth, e.scrollWidth), Math.max(b.clientWidth, e.clientWidth))
        }
    }
} ();