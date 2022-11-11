/**
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innovadeluxe SL
* @copyright 2016 Innovadeluxe SL

* @license   INNOVADELUXE
*/

class IdxrcookiesFront {
  constructor() {
    this.config =
      typeof IdxrcookiesConfigFront == "object" ? IdxrcookiesConfigFront : {};
    if (typeof this.config.urlAjax == "undefined") {
      throw "Variables de configuración necesarias no definidas";
    }
  }

  init() {
    this.handleClickEvents();
    this.handleCookieSwitchs();
    if (this.config.audit) {
      this.audit();
    } else {
      this.checkCookie();
    }
  }

  async audit() {
    try {
      await this.displayProcessBar();
      var cookies = this.get_cookies_array();
      let data = {
        audit: true,
        cookies: cookies,
        action: "Audit",
      };
      await this.ajaxRequest(this.config.urlAjax, data);
      window.location.replace(this.config.audit_next_page);
    } catch (e) {
      alert("error where try to save audit in db");
    }
  }

  checkCookie() {
    var cookieName = this.config.userOptions.cookieName;
    var cookieExist = Cookies.get(cookieName);
    if (cookieExist == "accepted") {
      Cookies.set(this.config.userOptions.cookieName, null, { path: "/" });
    }
    if (cookieExist != null && cookieExist != "") {
      try {
        var cookieChk = JSON.parse(Cookies.get(cookieName));
      } catch (e) {
        var cookieChk = null;
      }
    }
    if (cookieChk != null && cookieChk != "") {
      if (cookieChk.banned.length) {
        setTimeout(async () => {
          cookieChk.banned.forEach((ban) => {
            try {
              this.config.cookies_list.forEach(function (cook) {
                if (cook.id_cookie === ban) {
                  Cookies.set(cook.name, null, { domain: cook.domain });
                }
              });
            } catch (e) {}
          });
        }, 1000);
      }
      if (
        typeof this.config.forceDialog == "boolean" &&
        this.config.forceDialog == true
      ) {
        this.displayNotification();
      }
      //Update date cookie
    } else {
      this.displayNotification();
    }
  }

  displayNotification() {
    if (this.config.userOptions.blockUserNav) {
      $("body").addClass("idxrcookies-block-user-nav");
    }
    let clase = this;
    $(".cookie-button").hide();
    $("#divPosition").attr("id", this.config.userOptions.divPosition);
    $("#" + this.config.userOptions.divPosition + " .contenido").css(
      "background-color",
      this.config.userOptions.divColor
    );
    $("#textDiv").css("color", this.config.userOptions.textColor);
    $("#" + this.config.userOptions.divPosition).css(
      "color",
      this.config.userOptions.textColor
    );
    $("#textDiv").append(
      this.urldecode(this.nl2br(this.config.userOptions.cookiesText, true))
    );
    $("#idxrcookiesOK").text(this.config.userOptions.okText);
    if (this.config.userOptions.reject_button) {
      $("#idxrcookiesKO").text(this.config.userOptions.koText);
    } else {
      $("#idxrcookiesKO").remove();
    }
    if (this.config.userOptions.accept_selected_button) {
      $("#idxrcookiesPartial").text(this.config.userOptions.acceptSelectedText);
    } else {
      $("#idxrcookiesPartial").remove();
    }
    $("#cookies").attr(
      "href",
      this.urldecode(this.nl2br(this.config.userOptions.cookiesUrl))
    );
    $("span#text").append(
      this.decodeEntities(this.nl2br(this.config.userOptions.cookiesUrlTitle))
    );

    var message = $("#contentidxrcookies").html();

    jQuery("body").prepend(message);
  }

  displayProcessBar() {
    let promise = new Promise((resolve, reject) => {
      var message = $("#contentDeluxecookiesAudit").html();
      jQuery("body").prepend(message);
      var progress = 0;
      this.progressBarInterval = window.setInterval(() => {
        if (progress < 100) {
          progress += 1;
        } else {
          clearInterval(this.progressBarInterval);
          resolve(true);
        }
        $("#audit-progress-text")
          .find("strong")
          .html(progress + "%");
        $("#audit-progress-text")
          .parent()
          .css("width", progress + "%");
      }, 50);
    });

    return promise;
  }

  handleCookieSwitchs() {
    $(document).on("change", "#cookieModal .switch", function () {
      let template = $(this).data("template");
      let modulo = $(this).data("modulo");
      if (modulo != "") {
        var equalSwitchs = $(
          '#cookieModal .switch[data-modulo="' + modulo + '"]'
        );
      } else if (Number(template) > 0) {
        var equalSwitchs = $(
          '#cookieModal .switch[data-template="' + template + '"]'
        );
      }
      if (typeof equalSwitchs === "undefined") {
        return;
      }
      if ($(this).prop("checked") === false) {
        equalSwitchs.removeAttr("checked");
      } else {
        equalSwitchs.prop("checked", true);
      }
    });
  }

  handleClickEvents() {
    let clase = this;
    $(document).on("click", "#cookiesConf", ".cookiesConf", function () {
      clase.openFancybox();
    });

    $(document).on("click", "#idxrcookiesKO", function (e) {
      e.preventDefault();
      let cookies = clase.config.cookies_list;
      if (typeof cookies.length === "undefined") {
        return;
      }
      let banned = [];
      cookies.forEach((cookie) => {
        if (cookie.imperative === false) {
          banned.push(Number(cookie.id_cookie));
        }
      });
      clase.setCookieDeluxe(clase.config.userOptions.cookieName, 365, banned);
      $("body").removeClass("idxrcookies-block-user-nav");
      if (clase.config.userOptions.reload) {
        window.location.reload();
      }
    });

    $(document).on("click", "#idxrcookiesOK", function () {
      var cookieName = clase.config.userOptions.cookieName;
      var cookieExist = Cookies.get(cookieName);
      $("#cookieModal .switch").each(function () {
        if ($(this).prop("disabled")) {
          return;
        }
        $(this).prop("checked", true);
      });
      clase.setCookieDeluxe(clase.config.userOptions.cookieName, 365);
      $("body").removeClass("idxrcookies-block-user-nav");
      if (clase.config.userOptions.reload) {
        window.location.reload();
      } else {
        clase.renderAjaxTemplates(!cookieExist);
      }
    });

    $(document).on("click", "#idxrcookiesPartial", function () {
      var cookieName = clase.config.userOptions.cookieName;
      var cookieExist = Cookies.get(cookieName);
      clase.setCookieDeluxe(clase.config.userOptions.cookieName, 365);
      $("body").removeClass("idxrcookies-block-user-nav");
      if (clase.config.userOptions.reload) {
        window.location.reload();
      } else {
        clase.renderAjaxTemplates(!cookieExist);
      }
    });

    $(".cookiesConfButton").on("click", function () {
      clase.setCookiesSwitch();
      clase.openFancybox();
    });

    $(document).on("click", ".dlxctab-row", function () {
      var id = $(this).attr("data-id");
      $(".dlxctab-content:visible").hide();
      $('.dlxctab-content[data-tab="' + id + '"]').show();
      $(".dlxctab-row").removeClass("active");
      $(this).addClass("active");
    });

    $(document).on("click", "#js-save-cookieconf", function () {
      var cookieName = clase.config.userOptions.cookieName;
      var cookieExist = Cookies.get(cookieName);
      clase.setCookieDeluxe(clase.config.userOptions.cookieName, 365);
      $("body").removeClass("idxrcookies-block-user-nav");
      $.fancybox.close();
      if (clase.config.userOptions.reload) {
        window.location.reload();
      } else {
        clase.renderAjaxTemplates(!cookieExist);
      }
    });
  }

  async renderAjaxTemplates(force = false) {
    var cookieName = this.config.userOptions.cookieName;
    var cookieExist = Cookies.get(cookieName);
    if (cookieExist && !force) {
      return;
    }
    let banned = [];
    $("#cookieModal .switch").each(function () {
      if ($(this).prop("disabled")) {
        return;
      }
      if ($(this).prop("checked") == false) {
        var id = $(this).data("idcookie");
        if (banned.indexOf(id) < 0) {
          banned.push(id);
        }
      }
    });
    const rcpgTagManagerVars =
      typeof IdxrcookiesConfigRcpgTagManager !== "undefined"
        ? IdxrcookiesConfigRcpgTagManager
        : [];
    let data = {
      banned: banned,
      action: "getAjaxTemplates",
      php_self: this.config.php_self,
      id_product: this.config.id_product,
      rcpgTagManagerVars: rcpgTagManagerVars,
    };
    try {
      const response = await this.ajaxRequest(
        this.config.urlAjax,
        data,
        "post"
      );
      if (response.scripts.length) {
        for (let script of response.scripts) {
          const scriptElement = document.createElement("script");
          scriptElement.src = script;
          document.getElementsByTagName("head")[0].appendChild(scriptElement);
          scriptElement.onload = () => {
            $("head").append(response.header);
            $("body").append(response.footer);
          };
        }
      } else {
        $("head").append(response.header);
        $("body").append(response.footer);
      }
    } catch (e) {
      console.warn(e);
    }
  }

  setCookiesSwitch(forceOff = false) {
    var cookieChk;
    try {
      cookieChk = JSON.parse(Cookies.get(this.config.userOptions.cookieName));
    } catch (e) {
      cookieChk = {};
    }
    $("#cookieModal .switch").each(function () {
      var switch_id = $(this).attr("data-idcookie");
      var off = false;
      if (typeof cookieChk.banned != "undefined") {
        cookieChk.banned.forEach(function (ban) {
          if (switch_id == ban && forceOff === false) {
            off = true;
          }
        });
      }
      if (off) {
        $(this).removeAttr("checked");
      } else {
        $(this).attr("checked", "checked");
      }
    });
  }

  openFancybox() {
    $("#cookieModal .switch").attr("type", "checkbox");
    $("#cookieConfigurator #cookieModalList ul li").removeClass("active");
    $("#cookieConfigurator #cookieModalList ul li:first-child").addClass(
      "active"
    );
    $("#cookieConfigurator #cookieModalContent > div").css("display", "none");
    $("#cookieConfigurator #cookieModalContent > div:first-child").css(
      "display",
      "block"
    );
    $.fancybox.open(
      [
        {
          type: "inline",
          width: "500px",
          autoScale: true,
          minHeight: 30,
          content: $("#cookieConfigurator").html(),
        },
      ],
      {
        padding: 0,
      }
    );
  }

  getCookie(c_name) {
    var i,
      x,
      y,
      ARRcookies = document.cookie.split(";");
    for (i = 0; i < ARRcookies.length; i++) {
      x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
      y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
      x = x.replace(/^\s+|\s+$/g, "");
      if (x == c_name) return unescape(y);
    }
    return null;
  }

  _setCookieDeluxe(name, value, exp_y, exp_m, exp_d, path, domain, secure) {
    var cookie_string = name + "=" + escape(value);

    if (exp_y) {
      var expires = new Date(exp_y, exp_m, exp_d);
      cookie_string += "; expires=" + expires.toGMTString();
    }

    if (path) cookie_string += "; path=" + escape(path);
    if (domain) cookie_string += "; domain=" + escape(domain);
    if (secure) cookie_string += "; secure";

    document.cookie = cookie_string;
  }

  setCookieDeluxe(name, exdays, banned = null) {
    var c_expires = new Date();
    c_expires.setDate(c_expires.getDate() + exdays);
    if (banned === null) {
      banned = [];
      $("#cookieModal .switch").each(function () {
        if ($(this).prop("disabled")) {
          return;
        }
        if ($(this).prop("checked") == false) {
          var id = $(this).data("idcookie");
          if (banned.indexOf(id) < 0) {
            banned.push(id);
          }
        }
      });
    }
    var c_payload = {
      accepted: true,
      banned: banned,
      date: this.config.userOptions.date,
    };
    var json_str = JSON.stringify(c_payload);
    Cookies.set(name, json_str, { expires: 365 });
    var deluxecookies = document.getElementById("idxrcookies");
    if (deluxecookies) {
      deluxecookies.innerHTML = "";
      $("#contentDeluxecookies").remove();
      if (this.config.userOptions.fixed_button) {
        $(".cookie-button").show();
      }
    }
  }

  nl2br(str, is_xhtml) {
    var breakTag =
      is_xhtml || typeof is_xhtml === "undefined" ? "<br />" : "<br>";
    return (str + "").replace(
      /([^>\r\n]?)(\r\n|\n\r|\r|\n)/g,
      "$1" + breakTag + "$2"
    );
  }

  urldecode(str) {
    return decodeURIComponent((str + "").replace(/\+/g, "%20"));
  }

  decodeEntities(encodedString) {
    var textArea = document.createElement("textarea");
    textArea.innerHTML = encodedString;
    return textArea.value;
  }

  get_cookies_array() {
    var cookies = {};
    if (!document.cookie.length) {
      return cookies;
    }
    var pairs = document.cookie.split(";");
    for (var i = 0; i < pairs.length; i++) {
      var pair = pairs[i].split("=");
      cookies[(pair[0] + "").trim()] = unescape(pair[1]);
    }
    return cookies;
  }

  ajaxRequest(endpoint = "", datos = {}, tipo = "get", datatype = "json") {
    let promise = new Promise((resolve, reject) => {
      $.ajax({
        type: tipo,
        data: datos,
        dataType: datatype,
        url: endpoint,
        success: function (response) {
          resolve(response);
        },
        error: function (xhr, ajaxOptions, thrownError) {
          reject(thrownError);
        },
      });
    });
    return promise;
  }
}

$(function () {
  try {
    let handler;
    if (typeof IdxrcookiesFrontOverride == "function") {
      handler = new IdxrcookiesFrontOverride();
    } else {
      handler = new IdxrcookiesFront();
    }
    handler.init();
  } catch (e) {
    console.log(e);
  }
});
