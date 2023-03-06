!function(){function t(t,o){var n="undefined"!=typeof Symbol&&t[Symbol.iterator]||t["@@iterator"];if(!n){if(Array.isArray(t)||(n=function(t,o){if(t){if("string"==typeof t)return e(t,o);var n=Object.prototype.toString.call(t).slice(8,-1);return"Object"===n&&t.constructor&&(n=t.constructor.name),"Map"===n||"Set"===n?Array.from(t):"Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)?e(t,o):void 0}}(t))||o&&t&&"number"==typeof t.length){n&&(t=n);var r=0,c=function(){};return{s:c,n:function(){return r>=t.length?{done:!0}:{done:!1,value:t[r++]}},e:function(t){throw t},f:c}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}var a,i=!0,l=!1;return{s:function(){n=n.call(t)},n:function(){var t=n.next();return i=t.done,t},e:function(t){l=!0,a=t},f:function(){try{i||null==n.return||n.return()}finally{if(l)throw a}}}}function e(t,e){(null==e||e>t.length)&&(e=t.length);for(var o=0,n=new Array(e);o<e;o++)n[o]=t[o];return n}window.addEventListener("DOMContentLoaded",(function(){var e=function(t){if(!t)return t;var e=t.toString().toLowerCase().replace(/&(amp;)/g,"").replace(/&(mdash;)/g,"").replace(/\u2013|\u2014/g,"").replace(/[&]nbsp[;]/gi,"-").replace(/\s+/g,"-").replace(/[&\/\\#,^!+()$~%.'":*?<>{}@‘’”“]/g,"").replace(/\-\-+/g,"-").replace(/^-+/,"").replace(/-+$/,"");return decodeURI(encodeURIComponent(e))};({init:function(){this._run(),this._scroll(),this._toggleCollapse(),this._scrollToTop(),this._hide(),this._show(),this._hideOnMobileView()},_toggleCollapse:function(){var e,o=t(document.querySelectorAll(".eb-toc-container"));try{var n=function(){var t=e.value,o="true"===t.getAttribute("data-sticky");if("true"===t.getAttribute("data-collapsible")){var n=t.querySelector(".eb-toc-title"),r=t.querySelector(".eb-toc-wrapper");o||n.addEventListener("click",(function(){r.classList.toggle("hide-content")}))}};for(o.s();!(e=o.n()).done;)n()}catch(t){o.e(t)}finally{o.f()}},_scrollToTop:function(){var e=document.querySelector(".eb-toc-container"),o=e&&"true"==e.getAttribute("data-scroll-top"),n=e&&"true"==e.getAttribute("data-sticky"),r=e.getAttribute("data-scroll-target"),c=document.querySelector(".eb-toc-wrapper").getAttribute("data-top-offset");if(o){var a=function(){u.classList.remove("show-scroll"),u.classList.add("hide-scroll")},i=function(){u.classList.remove("hide-scroll"),u.classList.add("show-scroll")},l=function(){document.body.scrollTop>30||document.documentElement.scrollTop>20?i():a()},u=document.createElement("span");u.setAttribute("class","eb-toc-go-top "),u.innerHTML=">",document.body.insertBefore(u,document.body.lastChild),u.addEventListener("click",(function(){if(n||"scroll_to_toc"!==r)window.scroll({top:0,left:0,behavior:"smooth"});else{var t=c?-Math.abs(c):0,o=e.getBoundingClientRect().top+window.pageYOffset+t;window.scroll({top:o,behavior:"smooth"})}}));var s,d=t(document.querySelectorAll(".eb-toc-container"));try{for(d.s();!(s=d.n()).done;)"true"===s.value.getAttribute("data-scroll-top")?(window.addEventListener("scroll",l),i()):a()}catch(t){d.e(t)}finally{d.f()}}},_scroll:function(){var e,o=t(document.querySelectorAll(".eb-toc-wrapper"));try{var n=function(){var t=e.value,o="true"===t.getAttribute("data-smooth"),n=parseFloat(t.getAttribute("data-top-offset"));o&&t.querySelectorAll('a[href^="#"]').forEach((function(t){t.addEventListener("click",(function(t){var e=this.getAttribute("href").replace("#","");if(t.preventDefault(),"number"==typeof n){var o=n?-Math.abs(n):0,r=document.getElementById(e).getBoundingClientRect().top+window.pageYOffset+o;window.scrollTo({top:r,behavior:"smooth"})}else document.getElementById(e).scrollIntoView({behavior:"smooth"})}))}))};for(o.s();!(e=o.n()).done;)n()}catch(t){o.e(t)}finally{o.f()}},_hide:function(){var e,o=t(document.querySelectorAll(".eb-toc-close"));try{var n=function(){var t=e.value;t.addEventListener("click",(function(){var e=t.closest(".eb-toc-container");e.classList.add("eb-toc-content-hidden"),e.classList.remove("eb-toc-content-visible")}))};for(o.s();!(e=o.n()).done;)n()}catch(t){o.e(t)}finally{o.f()}},_show:function(){var e,o=t(document.querySelectorAll(".eb-toc-button"));try{var n=function(){var t=e.value;t.addEventListener("click",(function(){var e=t.closest(".eb-toc-container");e.classList.remove("eb-toc-content-hidden"),e.classList.add("eb-toc-content-visible")}))};for(o.s();!(e=o.n()).done;)n()}catch(t){o.e(t)}finally{o.f()}},_run:function(){var o,n=t(document.querySelectorAll(".eb-toc-container"));try{var r=function(){var t=o.value;if(t){var n=t.style.border;window.ebTocBorder=n}var r=document.querySelector(".eb-toc-wrapper");if(r){var c=JSON.parse(r.getAttribute("data-headers")),a=JSON.parse(r.getAttribute("data-visible")),i=[];void 0!==a&&a.forEach((function(t,e){return!0===t?i.push("h"+(e+1)):null}));var l=null!==i?i.join(","):"",u=void 0!==l&&""!==l?document.body.querySelectorAll(l):document.body.querySelectorAll("h1, h2, h3, h4, h5, h6");void 0!==c&&0!==u.length&&c.forEach((function(t,o){var n=e(t.text);u.forEach((function(t,o){var r=e(t.textContent);0===n.localeCompare(r)&&(t.innerHTML='<span id="'.concat(r,'" class="eb-toc__heading-anchor"></span>').concat(t.innerHTML))}))}))}};for(n.s();!(o=n.n()).done;)r()}catch(t){n.e(t)}finally{n.f()}},_hideOnMobileView:function(){var t=document.querySelector(".eb-toc-container");if(t){var e="true"===t.getAttribute("data-sticky"),o="true"==t.getAttribute("data-hide-mobile");e&&o&&window.screen.width<420&&(t.style.display="none")}}}).init()}))}();