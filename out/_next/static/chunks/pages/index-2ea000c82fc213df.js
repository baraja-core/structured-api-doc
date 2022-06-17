(self.webpackChunk_N_E=self.webpackChunk_N_E||[]).push([[405],{8312:function(e,n,r){(window.__NEXT_P=window.__NEXT_P||[]).push(["/",function(){return r(7603)}])},7603:function(e,n,r){"use strict";r.r(n),r.d(n,{default:function(){return X}});var t,i=r(5893),s=r(7294),o=r(9226),c=r(8456),l=r(8377),a=r(9520),d=r(6886),u=r(499),x=r(5567),h=r(1237),p=r(8046),f=r(233),m=r(9620),j=r(5113),g=r(4071),b=r(4178),Z=r(5861),v=r(9334),y=r(8885),w=function(e){var n=e.endpoints,r=e.activeEndpoint,t=e.isMenuOpen,s=e.setEndpoint,o=e.setMenuOpen;return(0,i.jsxs)(j.Z,{sx:{width:320,maxWidth:"100%"},children:[(0,i.jsxs)(d.ZP,{container:!0,children:[(0,i.jsx)(d.ZP,{item:!0,xs:t?10:6,children:(0,i.jsx)(b.Z,{sx:{textAlign:"center"},onClick:function(){return s(void 0)},children:t?(0,i.jsxs)(i.Fragment,{children:[(0,i.jsx)(y.Z,{children:(0,i.jsx)(x.Z,{})}),t&&(0,i.jsx)(v.Z,{children:"Overview"})]}):(0,i.jsx)(x.Z,{})})}),(0,i.jsx)(d.ZP,{item:!0,xs:t?2:6,children:(0,i.jsx)(b.Z,{sx:{textAlign:"center"},onClick:o,children:t?(0,i.jsx)(h.Z,{}):(0,i.jsx)(p.Z,{})})})]}),(0,i.jsx)(g.Z,{children:n.map((function(e){return(0,i.jsx)(b.Z,{onClick:function(){return s(e.class)},sx:e.class===r?{borderLeft:"3px solid #dc3545",borderRight:"3px solid #dc3545",background:"#eee"}:{borderLeft:"3px solid transparent",borderRight:"3px solid transparent"},children:t?(0,i.jsxs)(i.Fragment,{children:[(0,i.jsx)(y.Z,{children:e.public?(0,i.jsx)(f.Z,{}):(0,i.jsx)(m.Z,{})}),(0,i.jsx)(v.Z,{children:e.name}),e.actions.length>0&&(0,i.jsx)(Z.Z,{variant:"body2",color:"text.secondary",children:e.actions.length})]}):(0,i.jsx)(u.Z,{title:"".concat(e.name).concat(e.actions.length>0?" (".concat(e.actions.length,")"):""),placement:"right",children:(0,i.jsx)(y.Z,{children:e.public?(0,i.jsx)(f.Z,{}):(0,i.jsx)(m.Z,{})})})},e.class)}))})]})},O=r(3321);function S(e,n,r){return n in e?Object.defineProperty(e,n,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[n]=r,e}function P(e){for(var n=1;n<arguments.length;n++){var r=null!=arguments[n]?arguments[n]:{},t=Object.keys(r);"function"===typeof Object.getOwnPropertySymbols&&(t=t.concat(Object.getOwnPropertySymbols(r).filter((function(e){return Object.getOwnPropertyDescriptor(r,e).enumerable})))),t.forEach((function(n){S(e,n,r[n])}))}return e}!function(e){e.Public="public",e.Private="private"}(t||(t={}));var T=function(e){var n=e.visibility;return(0,i.jsx)(l.Z,{sx:P({padding:"1em"},n===t.Public?{color:"#155724",backgroundColor:"#d4edda",borderColor:"#c3e6cb"}:{color:"#856404",backgroundColor:"#fff3cd",borderColor:"#ffeeba"}),role:"alert",children:(0,i.jsxs)(d.ZP,{container:!0,rowSpacing:1,children:[(0,i.jsxs)(d.ZP,{item:!0,xs:10,children:[(0,i.jsx)(Z.Z,{component:"h3",sx:{fontSize:"16pt",fontWeight:"bold",display:"flex"},children:n===t.Public?(0,i.jsxs)(i.Fragment,{children:[(0,i.jsx)(f.Z,{})," This endpoint is public!"]}):(0,i.jsxs)(i.Fragment,{children:[(0,i.jsx)(m.Z,{})," This endpoint is private!"]})}),(0,i.jsx)(l.Z,{component:"p",sx:{marginBottom:0},children:n===t.Public?(0,i.jsx)(i.Fragment,{children:"This endpoint is\xa0publicly available from all over the\xa0Internet. Anyone can use, receive and send any data at\xa0any time. When implementing this endpoint, consider all security risks."}):(0,i.jsx)(i.Fragment,{children:"This endpoint is\xa0systemically protected. You must log in\xa0and have specific rights assigned to\xa0call this endpoint."})})]}),(0,i.jsx)(d.ZP,{item:!0,xs:2,sx:{textAlign:"right"},children:(0,i.jsx)(O.Z,{variant:"outlined",size:"small",href:"https://github.com/baraja-core/structured-api#-permissions",target:"_blank",children:"More info"})})]})})},A=r(2882),E=r(7906),k=r(3184),z=r(3816),C=r(3252),F=r(295),R=r(6576);function M(e,n){(null==n||n>e.length)&&(n=e.length);for(var r=0,t=new Array(n);r<n;r++)t[r]=e[r];return t}function _(e,n,r){return n in e?Object.defineProperty(e,n,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[n]=r,e}function q(e){for(var n=1;n<arguments.length;n++){var r=null!=arguments[n]?arguments[n]:{},t=Object.keys(r);"function"===typeof Object.getOwnPropertySymbols&&(t=t.concat(Object.getOwnPropertySymbols(r).filter((function(e){return Object.getOwnPropertyDescriptor(r,e).enumerable})))),t.forEach((function(n){_(e,n,r[n])}))}return e}function L(e,n){return function(e){if(Array.isArray(e))return e}(e)||function(e,n){var r=null==e?null:"undefined"!==typeof Symbol&&e[Symbol.iterator]||e["@@iterator"];if(null!=r){var t,i,s=[],o=!0,c=!1;try{for(r=r.call(e);!(o=(t=r.next()).done)&&(s.push(t.value),!n||s.length!==n);o=!0);}catch(l){c=!0,i=l}finally{try{o||null==r.return||r.return()}finally{if(c)throw i}}return s}}(e,n)||function(e,n){if(!e)return;if("string"===typeof e)return M(e,n);var r=Object.prototype.toString.call(e).slice(8,-1);"Object"===r&&e.constructor&&(r=e.constructor.name);if("Map"===r||"Set"===r)return Array.from(r);if("Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r))return M(e,n)}(e,n)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}var I,N=[{code:"GET",variant:"#3F51B5"},{code:"POST",variant:"#4CAF50"},{code:"CREATE",variant:"#4CAF50"},{code:"PUT",variant:"#FF9800"},{code:"PATCH",variant:"#FF9800"},{code:"DELETE",variant:"#fd3131"}],B={padding:".25em"},D=function(e){var n,r=e.route,t=e.action,s=e.actions;return(0,i.jsxs)(l.Z,{sx:{margin:"1em 0",border:"1px solid rgba(0,0,0,.125)",borderRadius:".25rem"},children:[(0,i.jsxs)(l.Z,{sx:{display:"flex",background:"rgba(0,0,0,.03)",padding:".75em",borderBottom:"1px solid rgba(0,0,0,.125)"},children:[(0,i.jsxs)(l.Z,{sx:{display:"flex",width:"80%"},children:[(0,i.jsx)(l.Z,{children:(0,i.jsx)(l.Z,{sx:{color:"white",background:null!==(I=null===(n=N.find((function(e){return e.code===t.httpMethod})))||void 0===n?void 0:n.variant)&&void 0!==I?I:"#007bff",fontSize:"10pt",paddingRight:".6em",paddingLeft:".6em",borderRadius:"10rem"},children:t.httpMethod})}),(0,i.jsx)(l.Z,{sx:{marginLeft:2},children:(0,i.jsxs)(l.Z,{sx:{color:"#555",fontFamily:"monospace",borderBottom:"1px dotted #555"},children:["api/v1/",r]})})]}),(0,i.jsx)(l.Z,{style:{width:"20%",textAlign:"right"},children:(null!==s&&void 0!==s?s:[]).map((function(e){return e}))})]}),(0,i.jsxs)(l.Z,{sx:{padding:"1em"},children:[t.description&&(0,i.jsxs)(i.Fragment,{children:[(0,i.jsx)("p",{style:{whiteSpace:"pre",lineHeight:"1.5",margin:0},children:t.description}),(0,i.jsx)(l.Z,{sx:{borderTop:"1px solid #ccc",margin:"1.5em 0"}})]}),(0,i.jsxs)(Z.Z,{sx:{fontWeight:"bold"},children:["GET"===t.httpMethod?"Query":"Body"," parameters:"]}),t.parameters.length>0?(0,i.jsx)(A.Z,{children:(0,i.jsxs)(E.Z,{size:"small",children:[(0,i.jsx)(k.Z,{children:(0,i.jsxs)(z.Z,{children:[(0,i.jsx)(C.Z,{sx:q({},B,{width:"2em"}),children:"#"}),(0,i.jsx)(C.Z,{sx:B,children:"Name"}),(0,i.jsx)(C.Z,{sx:B,children:"Type"}),(0,i.jsx)(C.Z,{sx:B,children:"Default"}),(0,i.jsx)(C.Z,{sx:B,children:"Description"}),(0,i.jsx)(C.Z,{sx:q({},B,{width:"120px"})})]})}),(0,i.jsx)(F.Z,{children:Object.entries(t.parameters).map((function(e){var n=L(e,2),r=n[0],t=n[1];return(0,i.jsxs)(z.Z,{children:[(0,i.jsx)(C.Z,{sx:q({},B,{color:"#6c757d"}),children:Number(r)+1}),(0,i.jsx)(C.Z,{sx:B,children:t.name}),(0,i.jsx)(C.Z,{sx:B,children:t.type.split("|").map((function(e){return(0,i.jsx)(l.Z,{component:"span",sx:{border:"1px solid #ccc",borderRadius:"6px",padding:"0 4px",marginRight:"2px"},children:e},e)}))}),(0,i.jsx)(C.Z,{sx:B,children:t.default&&(0,i.jsx)("code",{children:"string"===typeof t.default?t.default:(0,i.jsx)(l.Z,{component:"span",sx:{border:"1px solid #ccc",borderRadius:"6px",padding:"0 4px",marginRight:"2px",fontFamily:"monospace"},children:JSON.stringify(t.default)})})}),(0,i.jsx)(C.Z,{sx:B,children:t.description}),(0,i.jsx)(C.Z,{sx:q({},B,{textAlign:"right"}),children:t.required?(0,i.jsxs)(Z.Z,{component:"span",sx:{color:"#dc3545",textAlign:"right",fontSize:"10pt"},children:[(0,i.jsx)(R.Z,{sx:{fontSize:"14pt",marginRight:"4px"}}),"Required"]}):(0,i.jsx)(Z.Z,{component:"i",sx:{color:"#6c757d",fontSize:"10pt"},children:"optional"})})]},r)}))})]})}):(0,i.jsx)(Z.Z,{sx:{fontStyle:"italic",marginTop:1},children:"No parameters."})]})]})};function W(e,n){(null==n||n>e.length)&&(n=e.length);for(var r=0,t=new Array(n);r<n;r++)t[r]=e[r];return t}function V(e,n){return function(e){if(Array.isArray(e))return e}(e)||function(e,n){var r=null==e?null:"undefined"!==typeof Symbol&&e[Symbol.iterator]||e["@@iterator"];if(null!=r){var t,i,s=[],o=!0,c=!1;try{for(r=r.call(e);!(o=(t=r.next()).done)&&(s.push(t.value),!n||s.length!==n);o=!0);}catch(l){c=!0,i=l}finally{try{o||null==r.return||r.return()}finally{if(c)throw i}}return s}}(e,n)||function(e,n){if(!e)return;if("string"===typeof e)return W(e,n);var r=Object.prototype.toString.call(e).slice(8,-1);"Object"===r&&e.constructor&&(r=e.constructor.name);if("Map"===r||"Set"===r)return Array.from(r);if("Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r))return W(e,n)}(e,n)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}var G=function(e){var n=e.endpoint;return(0,i.jsxs)(l.Z,{children:[(0,i.jsx)("h1",{style:{marginTop:0},children:n.name}),n.description&&(0,i.jsx)("p",{children:n.description}),(0,i.jsx)(T,{visibility:n.public?t.Public:t.Private}),Object.entries(n.actions).map((function(e){var r=V(e,2),t=r[0],s=r[1];return(0,i.jsx)(D,{route:"".concat(n.route).concat("default"!==s.route?"/".concat(s.route):""),action:s,actions:[(0,i.jsx)(O.Z,{size:"small",variant:"outlined",sx:{padding:"0"},children:"TS"})]},t)}))]})},U=function(e){var n=e.endpoints,r=e.setEndpoint;return(0,i.jsxs)(i.Fragment,{children:[(0,i.jsx)("h1",{style:{marginTop:0},children:"Welcome to REST API Documentation"}),(0,i.jsx)(o.Z,{sx:{display:"flex",flexWrap:"wrap"},children:n.map((function(e){return(0,i.jsx)(o.Z,{sx:{width:"calc(100%/3)"},children:(0,i.jsx)(o.Z,{sx:{border:"1px solid rgba(0,0,0,.125)",borderRadius:".25rem",margin:".5em .5em"},onClick:function(){return r(e.class)},children:(0,i.jsxs)(b.Z,{children:[e.name,e.description&&(0,i.jsx)("p",{children:e.description})]})})},e.class)}))})]})},H=function(e){var n=e.height;return(0,i.jsxs)("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 255.85 77.92",style:{height:"".concat(null!==n&&void 0!==n?n:32,"px")},children:[(0,i.jsx)("path",{d:"M92.2 62.36L91.99.1h7.5q0 8.37 0 16.74a27.92 27.92 0 0 1 12.53-2.57 15.8 15.8 0 0 1 8 2 13.64 13.64 0 0 1 3.1 2.55q4.15 4.59 4.16 12.51v13.5q0 7.83-4.16 12.42a15.44 15.44 0 0 1-3.09 2.55c-4 2.51-7.79 2.42-16.16 2.47-2.42-.03-6.46 0-11.67.09zm17.3-7.07a10 10 0 0 0 7.5-2.83c1.83-1.89 2.75-4.62 2.75-8.17V31.83q0-5.34-2.75-8.17a10 10 0 0 0-7.5-2.83 9.5 9.5 0 0 0-7.3 2.91c-1.8 1.95-2.71 4.65-2.71 8.09v12.51c0 3.44.91 6.14 2.71 8.08a9.47 9.47 0 0 0 7.3 2.87zm27.02 7.07V16.51h7.34a45 45 0 0 1 12.08-.83c5.16.34 7.48 1.41 9 2.41a15 15 0 0 1 2.54 2.13q4.29 4.55 4.29 12.3v2.75h-7.44v-2.09q0-5.41-2.63-8.29c-1.75-1.92-4.23-2.88-7.46-2.88a9.67 9.67 0 0 0-7.46 2.92c-1.81 1.94-2.71 4.7-2.71 8.25v29.18zm39.65 15.57v-6.92h10.76q5.16 0 8-2.8c1.92-1.86 2.88-4.49 2.88-7.87V23.9h-19.14v-6.83h26.66v43.27q0 8.09-5 12.83-5 4.74-13.4 4.76zm24.41-67.25a5.31 5.31 0 0 1-3.9-1.42 5.19 5.19 0 0 1-1.42-3.84 5.49 5.49 0 0 1 1.42-4 5.93 5.93 0 0 1 7.79 0 5.44 5.44 0 0 1 1.42 4 5.16 5.16 0 0 1-1.42 3.84 5.28 5.28 0 0 1-3.89 1.42zM34.45 62.1L0 43.83V39.1l34.45-18.27v4.89L10.33 38.25l-3.5 1.72-2.78 1.39 2.86 1.34 3.42 1.76 24.09 12.65z",fill:"#000"}),(0,i.jsx)("path",{d:"M49.95 62.27L72.44.2h4L53.95 62.27z",fill:"#e95736"}),(0,i.jsx)("path",{d:"M221.39 57.11l24.09-12.65 3.43-1.76 2.86-1.34-2.78-1.39-3.51-1.72-24.09-12.58v-4.84l34.46 18.29v4.71l-34.46 18.3z",fill:"#000"})]})},J=function(e){var n=e.endpoints,r=(0,s.useState)(),t=r[0],o=r[1],c=(0,s.useState)(!0),d=c[0],u=c[1],x=n.find((function(e){return e.class===t}));return(0,i.jsx)(l.Z,{sx:{margin:"3em 0"},children:(0,i.jsxs)(a.Z,{children:[(0,i.jsxs)(l.Z,{sx:{display:"flex"},children:[(0,i.jsx)(l.Z,{sx:{width:d?"20%":"8%"},children:(0,i.jsx)(w,{endpoints:n,activeEndpoint:t,isMenuOpen:d,setEndpoint:o,setMenuOpen:function(){return u(!d)}})}),(0,i.jsx)(l.Z,{sx:{padding:"0 1.5em"},children:x?(0,i.jsx)(G,{endpoint:x}):(0,i.jsx)(U,{endpoints:n,setEndpoint:o})})]}),(0,i.jsxs)(l.Z,{sx:{textAlign:"center",margin:"1em",marginTop:"2em",paddingTop:"1em",borderTop:"1px solid #ccc"},children:[(0,i.jsx)(H,{height:20}),(0,i.jsxs)(l.Z,{sx:{fontSize:"10pt",marginTop:"1em"},children:["Generated by\xa0",(0,i.jsx)("a",{href:"https://github.com/baraja-core/structured-api-doc",target:"_blank",rel:"noreferrer",children:"BRJ documentation"}),"."]})]})]})})},X=function(){var e=(0,s.useState)(),n=e[0],r=e[1];return(0,s.useEffect)((function(){var e=document.getElementById("brj-endpoint-url");e?fetch("".concat(e.textContent)).then((function(e){return e.json()})).then((function(e){return r(e.endpoints)})):r([])}),[]),n?(0,i.jsx)(J,{endpoints:n}):(0,i.jsxs)(o.Z,{sx:{textAlign:"center",margin:"8em 1em"},children:[(0,i.jsx)(c.Z,{}),(0,i.jsx)(o.Z,{sx:{marginTop:"2em"},children:(0,i.jsx)(H,{height:20})})]})}}},function(e){e.O(0,[125,774,888,179],(function(){return n=8312,e(e.s=n);var n}));var n=e.O();_N_E=n}]);