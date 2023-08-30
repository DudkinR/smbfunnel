var global_post_requests = [];
function ajaxRequest() {
  this.HtmlEntities = function () {};

  this.HtmlEntities.map = {
    "'": "&apos;",
    "<": "&lt;",
    ">": "&gt;",
    " ": "&nbsp;",
    "¡": "&iexcl;",
    "¢": "&cent;",
    "£": "&pound;",
    "¤": "&curren;",
    "¥": "&yen;",
    "¦": "&brvbar;",
    "§": "&sect;",
    "¨": "&uml;",
    "©": "&copy;",
    ª: "&ordf;",
    "«": "&laquo;",
    "¬": "&not;",
    "®": "&reg;",
    "¯": "&macr;",
    "°": "&deg;",
    "±": "&plusmn;",
    "²": "&sup2;",
    "³": "&sup3;",
    "´": "&acute;",
    µ: "&micro;",
    "¶": "&para;",
    "·": "&middot;",
    "¸": "&cedil;",
    "¹": "&sup1;",
    º: "&ordm;",
    "»": "&raquo;",
    "¼": "&frac14;",
    "½": "&frac12;",
    "¾": "&frac34;",
    "¿": "&iquest;",
    À: "&Agrave;",
    Á: "&Aacute;",
    Â: "&Acirc;",
    Ã: "&Atilde;",
    Ä: "&Auml;",
    Å: "&Aring;",
    Æ: "&AElig;",
    Ç: "&Ccedil;",
    È: "&Egrave;",
    É: "&Eacute;",
    Ê: "&Ecirc;",
    Ë: "&Euml;",
    Ì: "&Igrave;",
    Í: "&Iacute;",
    Î: "&Icirc;",
    Ï: "&Iuml;",
    Ð: "&ETH;",
    Ñ: "&Ntilde;",
    Ò: "&Ograve;",
    Ó: "&Oacute;",
    Ô: "&Ocirc;",
    Õ: "&Otilde;",
    Ö: "&Ouml;",
    "×": "&times;",
    Ø: "&Oslash;",
    Ù: "&Ugrave;",
    Ú: "&Uacute;",
    Û: "&Ucirc;",
    Ü: "&Uuml;",
    Ý: "&Yacute;",
    Þ: "&THORN;",
    ß: "&szlig;",
    à: "&agrave;",
    á: "&aacute;",
    â: "&acirc;",
    ã: "&atilde;",
    ä: "&auml;",
    å: "&aring;",
    æ: "&aelig;",
    ç: "&ccedil;",
    è: "&egrave;",
    é: "&eacute;",
    ê: "&ecirc;",
    ë: "&euml;",
    ì: "&igrave;",
    í: "&iacute;",
    î: "&icirc;",
    ï: "&iuml;",
    ð: "&eth;",
    ñ: "&ntilde;",
    ò: "&ograve;",
    ó: "&oacute;",
    ô: "&ocirc;",
    õ: "&otilde;",
    ö: "&ouml;",
    "÷": "&divide;",
    ø: "&oslash;",
    ù: "&ugrave;",
    ú: "&uacute;",
    û: "&ucirc;",
    ü: "&uuml;",
    ý: "&yacute;",
    þ: "&thorn;",
    ÿ: "&yuml;",
    Œ: "&OElig;",
    œ: "&oelig;",
    Š: "&Scaron;",
    š: "&scaron;",
    Ÿ: "&Yuml;",
    ƒ: "&fnof;",
    ˆ: "&circ;",
    "˜": "&tilde;",
    Α: "&Alpha;",
    Β: "&Beta;",
    Γ: "&Gamma;",
    Δ: "&Delta;",
    Ε: "&Epsilon;",
    Ζ: "&Zeta;",
    Η: "&Eta;",
    Θ: "&Theta;",
    Ι: "&Iota;",
    Κ: "&Kappa;",
    Λ: "&Lambda;",
    Μ: "&Mu;",
    Ν: "&Nu;",
    Ξ: "&Xi;",
    Ο: "&Omicron;",
    Π: "&Pi;",
    Ρ: "&Rho;",
    Σ: "&Sigma;",
    Τ: "&Tau;",
    Υ: "&Upsilon;",
    Φ: "&Phi;",
    Χ: "&Chi;",
    Ψ: "&Psi;",
    Ω: "&Omega;",
    α: "&alpha;",
    β: "&beta;",
    γ: "&gamma;",
    δ: "&delta;",
    ε: "&epsilon;",
    ζ: "&zeta;",
    η: "&eta;",
    θ: "&theta;",
    ι: "&iota;",
    κ: "&kappa;",
    λ: "&lambda;",
    μ: "&mu;",
    ν: "&nu;",
    ξ: "&xi;",
    ο: "&omicron;",
    π: "&pi;",
    ρ: "&rho;",
    ς: "&sigmaf;",
    σ: "&sigma;",
    τ: "&tau;",
    υ: "&upsilon;",
    φ: "&phi;",
    χ: "&chi;",
    ψ: "&psi;",
    ω: "&omega;",
    ϑ: "&thetasym;",
    ϒ: "&Upsih;",
    ϖ: "&piv;",
    "–": "&ndash;",
    "—": "&mdash;",
    "‘": "&lsquo;",
    "’": "&rsquo;",
    "‚": "&sbquo;",
    "“": "&ldquo;",
    "”": "&rdquo;",
    "„": "&bdquo;",
    "†": "&dagger;",
    "‡": "&Dagger;",
    "•": "&bull;",
    "…": "&hellip;",
    "‰": "&permil;",
    "′": "&prime;",
    "″": "&Prime;",
    "‹": "&lsaquo;",
    "›": "&rsaquo;",
    "‾": "&oline;",
    "⁄": "&frasl;",
    "€": "&euro;",
    ℑ: "&image;",
    "℘": "&weierp;",
    ℜ: "&real;",
    "™": "&trade;",
    ℵ: "&alefsym;",
    "←": "&larr;",
    "↑": "&uarr;",
    "→": "&rarr;",
    "↓": "&darr;",
    "↔": "&harr;",
    "↵": "&crarr;",
    "⇐": "&lArr;",
    "⇑": "&UArr;",
    "⇒": "&rArr;",
    "⇓": "&dArr;",
    "⇔": "&hArr;",
    "∀": "&forall;",
    "∂": "&part;",
    "∃": "&exist;",
    "∅": "&empty;",
    "∇": "&nabla;",
    "∈": "&isin;",
    "∉": "&notin;",
    "∋": "&ni;",
    "∏": "&prod;",
    "∑": "&sum;",
    "−": "&minus;",
    "∗": "&lowast;",
    "√": "&radic;",
    "∝": "&prop;",
    "∞": "&infin;",
    "∠": "&ang;",
    "∧": "&and;",
    "∨": "&or;",
    "∩": "&cap;",
    "∪": "&cup;",
    "∫": "&int;",
    "∴": "&there4;",
    "∼": "&sim;",
    "≅": "&cong;",
    "≈": "&asymp;",
    "≠": "&ne;",
    "≡": "&equiv;",
    "≤": "&le;",
    "≥": "&ge;",
    "⊂": "&sub;",
    "⊃": "&sup;",
    "⊄": "&nsub;",
    "⊆": "&sube;",
    "⊇": "&supe;",
    "⊕": "&oplus;",
    "⊗": "&otimes;",
    "⊥": "&perp;",
    "⋅": "&sdot;",
    "⌈": "&lceil;",
    "⌉": "&rceil;",
    "⌊": "&lfloor;",
    "⌋": "&rfloor;",
    "⟨": "&lang;",
    "⟩": "&rang;",
    "◊": "&loz;",
    "♠": "&spades;",
    "♣": "&clubs;",
    "♥": "&hearts;",
    "♦": "&diams;",
  };

  this.HtmlEntities.decode = (string) => {
    var entityMap = this.HtmlEntities.map;
    for (var key in entityMap) {
      var entity = entityMap[key];
      var regex = new RegExp(entity, "g");
      string = string.replace(regex, key);
    }
    string = string.replace(/&quot;/g, '"');
    string = string.replace(/&amp;/g, "&");
    return string;
  };

  this.HtmlEntities.encode = (string) => {
    var entityMap = this.HtmlEntities.map;
    string = string.replace(/&/g, "&amp;");
    string = string.replace(/"/g, "&quot;");
    for (var key in entityMap) {
      var entity = entityMap[key];
      var regex = new RegExp(key, "g");
      string = string.replace(regex, entity);
    }
    return string;
  };
  this.escHTML = function (data, type = "post") {
    data += "";
    if (type == "post") {
      data = this.HtmlEntities.encode(data);
    } else {
      var a = document.createElement("a");
      a.href = data;
      a.search = this.HtmlEntities.encode(a.search);
      data = a.href;
    }

    //console.log(data);
    return Base64.encode(data);
  };
  this.doDelayInPost = function (cb) {
    try {
      if (
        global_post_requests.length < 1 ||
        global_req_is_gcp === undefined ||
        global_req_is_gcp === false
      ) {
        //console.log("pot request granted");
        cb(1);
      } else {
        //console.log("pot request in hold");
        setTimeout(
          (cb) => {
            this.doDelayInPost(cb);
          },
          200,
          cb
        );
      }
    } catch (err) {
      console.log("something wrong with post request permission");
      cb(1);
      console.log(err);
    }
  };
  this.request_count = [];
  this.cb_for_loading_change = false;
  this.registerAjaxLoading = function (cb = false) {
    this.cb_for_loading_change = cb;
  };
  this.startRequest = function (start = true) {
    if (start) {
      this.request_count.push(1);
    } else {
      this.request_count.pop();
    }

    if (this.cb_for_loading_change !== false) {
      try {
        let stat = this.request_count.length > 0 ? true : false;
        this.cb_for_loading_change(stat);
      } catch (err) {
        console.log(err);
      }
    }
  };
  this.endRequest = function () {
    this.startRequest(false);
  };
  this.postRequestCb = async function (url, data, callback) {
    //alert(data);
    let _this = this;
    await new Promise((resolve, reject) => {
      this.doDelayInPost(function (data) {
        resolve(1);
      });
    });

    var data_ob = new FormData(document.createElement("form"));
    for (let i in data) {
      if (typeof data[i] === "object") {
        data_ob.append(i, data[i]);
      } else {
        data_ob.append(i, this.escHTML(data[i] + ""));
      }
    }
    data_ob.append("cfhttp", 1);
    //data=this.requestArranger(data);
    var thisclass = this;
    var srvr = new XMLHttpRequest();
    global_post_requests.push(1);
    _this.startRequest();
    srvr.onreadystatechange = function () {
      if (this.readyState == 4) {
        global_post_requests.pop();
        _this.endRequest();
        if (this.status == 200) {
          callback(thisclass.openLoginScreen(this.responseText));
        } else {
          try {
            console.error(`(${this.status}) ${this.statusText}`);
            callback("0");
          } catch (err) {}
        }
      }
    };
    srvr.open("POST", url, true);
    //srvr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    srvr.send(data_ob);
  };
  this.postRequestPromise = async function (url, data) {
    let _this = this;
    //Its gointo be used as image downloader only
    await new Promise((resolve, reject) => {
      this.doDelayInPost(function (data) {
        resolve(1);
      });
    });

    return new Promise((resolve, reject) => {
      var data_ob = new FormData(document.createElement("form"));
      for (let i in data) {
        data_ob.append(i, this.escHTML(data[i] + ""));
      }
      data_ob.append("cfhttp", 1);
      //data=this.requestArranger(data);
      var thisclass = this;
      var srvr = new XMLHttpRequest();
      _this.startRequest();
      srvr.onreadystatechange = function () {
        if (this.readyState == 4) {
          _this.endRequest();
          if (this.status == 200) {
            resolve(thisclass.openLoginScreen(this.responseText));
          } else {
            resolve({ err: this.status });
          }
        }
      };
      srvr.open("POST", url, true);
      //srvr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      srvr.send(data_ob);
    });
  };
  this.getRequestCb = function (url, callback) {
    let _this = this;
    url = this.escHTML(url, "get");
    var thisclass = this;
    var srvr = new XMLHttpRequest();
    _this.startRequest();
    srvr.onreadystatechange = function () {
      if (this.readyState == 4) {
        _this.endRequest();
      }
      if (this.readyState == 4 && this.status == 200) {
        callback(thisclass.openLoginScreen(this.responseText));
      }
    };
    srvr.open("GET", url, true);
    srvr.send();
  };

  this.postRequest = function (url, data) {
    let _this = this;
    data = this.requestArranger(data);
    var thisclass = this;
    var srvr = new XMLHttpRequest();

    _this.startRequest();
    srvr.onreadystatechange = function () {
      if (this.readyState == 4) {
        _this.endRequest();
      }
      if (this.readyState == 4 && this.status == 200) {
        thisclass.openLoginScreen(this.responseText);
      }
    };
    srvr.open("POST", url, true);
    srvr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    srvr.send(data);
  };

  this.getRequest = function (url) {
    let _this = this;
    url = this.requestArranger(url, "get");
    var thisclass = this;
    var srvr = new XMLHttpRequest();
    _this.startRequest();
    srvr.onreadystatechange = function () {
      if (this.readyState == 4) {
        _this.endRequest();
      }
      if (this.readyState == 4 && this.status == 200) {
        thisclass.openLoginScreen(this.responseText);
      }
    };
    srvr.open("GET", url, true);
    srvr.send();
  };

  this.createCSRF = function (callback) {
    this.postRequestCb("req.php", { checkcsrf: "create" }, function (data) {
      callback(data);
    });
  };
  this.matchCSRF = function (token, callback) {
    this.postRequestCb(
      "req.php",
      { checkcsrf: "match", token: token },
      function (data) {
        callback(data.trim());
      }
    );
  };
  this.setCookie = function (cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
  };

  this.getCookie = function (cname) {
    var name = cname + "=";
    var ca = document.cookie.split(";");
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == " ") {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  };

  this.loginscreen_opened = false;

  this.openLoginScreen = function (data) {
    try {
      let open = false;
      if (data.trim().length > 0) {
        if (data.trim() === "@not-logged-in@") {
          data = "Session Time Out";
          open = true;
        } else {
          this.loginscreen_opened = false;
          open = false;
        }

        if (open && !this.loginscreen_opened) {
          this.loginscreen_opened = true;
          window.open(
            "index.php?page=login&autologin=1",
            "_blank",
            "toolbar=yes,height=800,width=1600"
          );
        }
      }
    } catch (err) {
      console.log(err.message);
    }
    return data;
  };
}
function copyText(txt, isvar = 0) {
  //copy to clip board
  try {
    if (isvar !== 0) {
      txt = eval(txt);
    }
    var txtarea = document.createElement("textarea");
    txtarea.innerText = txt;
    document.body.appendChild(txtarea);
    txtarea.select();
    document.execCommand("copy");
    document.body.removeChild(txtarea);
  } catch (err) {
    console.log(err.message);
  }
}
function isJSON(str) {
  var data = false;
  try {
    var jsn = JSON.parse(str);
    data = jsn;
  } catch (err) {
    data = false;
  }
  return data;
}
function confirmDeletion(continues,e,count = 0, type = "Data") {	
	e.preventDefault();
	var message = t("Are you sure?");	
	if (count > 0 || count == "all") {
		var s = count > 1 || count == "all" ? "s" : "";
		var wheree = " funnel";
		if (type == "sequence") {
		wheree = " list";
		}
		message = "This " + type + " was used in " + count + wheree + s + ", are you sure about deleting this " + type;
	}		
		Swal.fire({
			title: message,
			text: t("You won't be able to revert this!"),
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#d33",
			cancelButtonColor: "#3085d6",
			confirmButtonText: t("Yes, delete it!"),
		}).then((result) => {
			if (result.isConfirmed) {						
				continues.submit();
			} else {			
				return false;
			}		
		});		
}
try {
  jQuery(document).ready(function ($) {
    $('[data-bs-toggle="tooltip"]').tooltip();
  });
} catch (err) {}
try {
  jQuery(document).ready(function ($) {
    try {
      if (document.getElementById("datesearchformdata") !== undefined) {
        $(".datesearchformdata").hide();
        var open = 1;
        $(".srchcontainer .btn").click(function () {
          if (open == 1) {
            $(".datesearchformdata").show(500);
            open = 0;
            oopen = 1;
          } else {
            $(".datesearchformdata").hide(500);
            open = 1;
          }
        });
        $(".datesearchformdata").click(function () {
          oopen = 1;
        });

        $(document).click(function () {
          try {
            if (oopen == 0) {
              $(".datesearchformdata").hide(500);
            }
            oopen = 0;
          } catch (errr) {}
        });
      } else {
        alert("undefined");
      }
    } catch (err) {
      console.log(err.message);
    }
  });
} catch (err) {
  console.log(err);
}

function validateDateField() {
  try {
    (function ($) {
      if ($("select[name=fromdays]").val() == 1) {
        $("#searchspancontainer").html("");
      }
    })(jQuery);
    return true;
  } catch (err) {
    console.log(err.message);
  }
}
try {
  jQuery(document).ready(function () {
    try {
      document.getElementById("historyback").onclick = function () {
        window.history.back();
      };
    } catch (errrr) {}
  });
} catch (err) {
  console.log(err);
}
function OnPageSearch(search = "", el = null) {
  this.search_data = search;
  this.el = el;
  this.url = "";
  this.start = "<!-- keyword search -->";
  this.end = "<!-- /keyword search -->";
  this.do_modify = true;
  this.search_parameter = "onpage_search";
  this.minsearch_len = 1;
  this.cb = null;
  try {
    if (window.req_onpage_search_parent === undefined && this.el !== null) {
      window.req_onpage_search_parent = document.querySelectorAll(
        this.el
      )[0].innerHTML;
    }
  } catch (errrr) {
    if (this.el !== null) {
      window.req_onpage_search_parent = document.querySelectorAll(
        this.el
      )[0].innerHTML;
    }
  }

  this.search = function () {
    var this_class = this;
    try {
      if (this.el !== null && this.search_data.length >= this.minsearch_len) {
        var request = new ajaxRequest();
        var search_ob = {};
        search_ob[this.search_parameter] = this.search_data;
        //console.log(search_ob);
        request.postRequestCb(this.url, search_ob, function (data) {
          try {
            data = data.trim();
            //console.log(data);
            var start = data.indexOf(this_class.start);
            var end = data.indexOf(this_class.end);
            if (!isNaN(start)) {
              start += this_class.start.length;
              data = data.substr(start, end - start);
              if (this_class.cb !== null) {
                setTimeout(function () {
                  this_class.cb(data);
                }, 10);
              }
              if (this_class.do_modify) {
                document.querySelectorAll(this_class.el)[0].innerHTML = data;
              } else {
                return data;
              }
            }
          } catch (errr) {
            console.log(errr.message);
          }
        });
      } else if (window.req_onpage_search_parent !== undefined) {
        if (this_class.cb !== null) {
          this_class.cb(window.req_onpage_search_parent);
        }
        if (this.do_modify) {
          document.querySelectorAll(this_class.el)[0].innerHTML =
            window.req_onpage_search_parent;
        } else {
          return window.req_onpage_search_parent;
        }
      }
    } catch (errr) {
      console.log(errr.message);
    }
  };
}
document.onreadystatechange = function () {
  if (this.readyState == "complete") {
    try {
      document.querySelectorAll(".qfnl_max_records_per_page")[0].onchange =
        function () {
          document
            .getElementsByClassName("qfnl_max_records_per_page_btn")[0]
            .click();
        };
    } catch (errr) {}
    try {
      var totalDataReg = /([0-9,])+/g;
      var toalsatael_text = document
        .querySelectorAll("td.total-data")[0]
        .innerText.trim();
      tataldatacount = toalsatael_text.match(totalDataReg)[0];
      //console.log(tataldatacount.replace(",","").trim());
      new ajaxRequest().postRequestCb(
        "req.php",
        { qfnl_current_page_maxdata: tataldatacount },
        function (data) {}
      );
    } catch (err) {
      console.log(err.message + "--");
    }
  }
};
function modifytitle(title, type) {
  var title = title;
  var type = type;

  if (title.length > 0) {
    document.title = t(title) + " - " + t(type) + " (CloudFunnels)";
  } else {
    document.title = t(type) + " (CloudFunnels)";
  }
}
function authPurchaseData(doc = 0) {
  //alert(doc);
  var request = new ajaxRequest();
  if (doc === 0) {
    //alert("test");
    data = { chkforauthvalidationpucrhase: 1 };
    request.postRequestCb("req.php", data, function (res) {});
  } else {
    doc.disabled = true;
    var email = document.getElementById("authcustemail").value;
    var ordercode = document.getElementById("authcustordercode").value;
    var errdoc = document.getElementById("authvalidationerr");
    errdoc.style.color = "green";
    errdoc.innerHTML = t("Processing, please wait...");
    var data = {
      chkforauthvalidationpucrhase: 1,
      auth_valid_user: email,
      auth_valid_order_code: ordercode,
    };
    request.postRequestCb("req.php", data, function (res) {
      res = res.trim();
      if (res == "1") {
        errdoc.innerHTML = t(
          "Successfully validated, redirecting you in a moment."
        );
        window.location.reload();
      } else {
        if (res === "0") {
          res = t("Unable to verify you, please re-check your credentials.");
        }
        errdoc.style.color = "#e6005c";
        errdoc.innerHTML = res;
        doc.disabled = false;
      }
    });
  }
}
function viewTutorial(url) {
  window.open(
    url,
    "_blank",
    "location=yes,height=600,width=800,scrollbars=yes,status=yes"
  );
}
function doEscapePopup(cb) {
  document.body.onkeyup = function (e) {
    try {
      if (e.key == "Escape" || e.code == "Escape") {
        setTimeout(function () {
          try {
            cb();
          } catch (err) {}
        }, 10);
      }
    } catch (err) {
      console.log(err.message);
    }
  };
}

//Media handler
var global_cf_media_export_callback = false;

function openMedia(cb = false, html = false) {
  cf_media_export_html = html;
  global_cf_media_export_callback = cb;
  try {
    document.querySelectorAll("#cf_media_app")[0].style.display = "block";
  } catch (err) {}
}
function closeMedia() {
  try {
    global_cf_media_export_callback = false;
    document.querySelectorAll("#cf_media_app")[0].style.display = "none";
  } catch (err) {}
}

//Media handler
