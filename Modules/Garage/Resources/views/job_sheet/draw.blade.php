      
<style>
  
/*!
 * Project: Asilify
 * Author: Dotted Limited
 * Version   :  1.8.0
 * Updated   :  07.23.2020
**/


/*Parsley*/
.parsley-errors-list {
  margin: 0;
  padding: 0; }
  .parsley-errors-list > li {
    list-style: none;
    color: #ff5c75;
    margin-top: 10px;
    padding: 4px 7px 4px 28px;
    position: relative;
    display: inline-block;
    background-color: rgba(255, 92, 117, 0.2);
    border-radius: 7px; }
    .parsley-errors-list > li:before {
      content: "â“˜";
      font-family: "Nioicon";
      position: absolute;
      left: 8px;
      top: 4px; }
    .parsley-errors-list > li:after {
      content: "";
      border: 8px solid transparent;
      border-bottom-color: rgba(255, 92, 117, 0.2);
      position: absolute;
      left: 14px;
      top: -16px; }

.parsley-error {
  border-color: #ff5c75; }

.parsley-success {
  border-color: #43d39e; }

  /*input*/
.hide-arrows input::-webkit-outer-spin-button, .hide-arrows input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/*mods*/
.wider .profile-ud-value, .wider .profile-ud-label {
    width: 50%;
}
.dent-scratch-color {
    border-radius: 50%;
    width: 20px;
    height: 20px;
    margin: 0 0 0 0 !important;
    padding: 0;
    border: 3px solid #8492a0;
}
.dent-scratch-color.red {
    border: 3px solid #ff0000;
    background: #ff0000;
}
.dent-scratch-color.blue {
    border: 3px solid #1418FF;
    background: #1418FF;
}
.dent-scratch-color.active {
    border: 3px solid #8492a0;
}
canvas#car-diagram {
    border-radius: 5px;
    background-color: transparent !important;
    cursor: pointer;
}
.car-diagram-holder {
    width: 100%;
    max-width: 600px;
    border: 2px solid #e6eaee;
    border-radius: 5px;
    background-color: #F7F7F7 !important;
    margin: 0 auto;
}



.hide-arrows input[type=number] {
  -moz-appearance: textfield;
}

/*Toastr*/
.sweet-alert button.cancel {
    background-color: transparent;
    border: 1px solid #ccc;
    color: #777;
}

.sweet-alert button.cancel:hover {
    background-color: rgba(0, 0, 0, .1);
}

.alert.alert-dismissable i {
    margin-right: 5px;
}

#toast-container>div {
    -moz-box-shadow: 0 10px 48px rgba(30, 107, 174, 0.1), 0 1px 1px rgba(255, 248, 254, 0.61);
    -webkit-box-shadow: 0 10px 48px rgba(30, 107, 174, 0.1), 0 1px 1px rgba(255, 248, 254, 0.61);
    box-shadow: 0 10px 48px rgba(30, 107, 174, 0.1), 0 1px 1px rgba(255, 248, 254, 0.61);
}

.toast-success {
    background-color: #00C853;
}

.toast-error {
    background-color: #ff1a1a;
}

#toast-container>div:hover {
    -moz-box-shadow: 0 10px 48px rgba(30, 107, 174, 0.1), 0 1px 1px rgba(255, 248, 254, 0.61);
    -webkit-box-shadow: 0 10px 48px rgba(30, 107, 174, 0.1), 0 1px 1px rgba(255, 248, 254, 0.61);
    box-shadow: 0 10px 48px rgba(30, 107, 174, 0.1), 0 1px 1px rgba(255, 248, 254, 0.61);
}
button.toast-close-button {
    margin-top: 6px;
}

/*empty*/
.empty {
    padding: 40px 0;
}
.empty em {
    font-size: 45px;
}
.empty p {
    margin-top: 10px;
}

/*helper classes*/
.pull-right {
  float: right !important;
}
.unset-mh {
  min-height: unset !important;
}

/*parts*/
.part-order-item {
    border: 1px solid #e2e7f1;
    margin-bottom: 15px;
    padding: 10px;
    background: #fff;
}
.part-drag {
    display: inline-block;
    border-right: 1px solid #e2e7f1;
    margin-right: 6px;
    cursor: move;
}


/*fuel Level*/
.fuel-slider {
  -webkit-appearance: none;
  width: 100%;
  height: 15px;
  border-radius: 5px;
  background: #e4efff;
  outline: none;
  opacity: 0.7;
  -webkit-transition: .2s;
  transition: opacity .2s;
  -webkit-transform: rotateY(180deg);
   -moz-transform: rotateY(180deg);
   -ms-transform: rotateY(180deg);
   -o-transform: rotateY(180deg);
   transform: rotateY(180deg);
}

.fuel-slider:hover {
  opacity: 1;
}

.fuel-slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 25px;
  height: 25px;
  border-radius: 50%;
  background: #0971fe;
  cursor: pointer;
}

.fuel-slider::-moz-range-thumb {
  width: 25px;
  height: 25px;
  border-radius: 50%;
  background: #0971fe;
  cursor: pointer;
}

.form-control-wrap.stacked {
    margin-bottom: 10px;
}
.form-control-wrap.stacked a {
    right: 10px;
    position: absolute;
    top: 7px;
}
ol.styled-list, ul.styled-list {
    list-style: none;
    margin: 0;
    padding: 0;
    list-style: unset;
    padding: revert;
}

/*select 2*/
.select2-container--default .select2-selection--multiple {
    border: 1px solid #dbdfea;
}
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border: solid #0971fe 1px;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
  background-color: #e4efff;
  border: 1px solid #9dc6ff;
  color: #0971fe;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #0971fe;
}
.select2-container .select2-selection--multiple {
    min-height: 42px;
}


/*bulk imports*/
.custom-control.custom-switch.aligned {
    margin-top: 30px !important;
}
textarea.form-control.unset-min-height {
    min-height: unset !important;
}



/*viewer*/

.document {
    min-height: 500px;
}

.document-map {
    width: 100%;
    display: none;
}
.signer-document div {
    overflow-x: auto;
}

.document-pagination {
    overflow: hidden;
    margin-bottom: 15px;
}
.document-pagination .launch-viewer {
    margin-left: 30px;
    text-align: center;
}
.btn-zoom i {
    margin-left: -3px;
}

.document-load {
    font-size: 24px;
    text-align: center;
    padding: 200px 0;
    width: 100%;
}
.document-load div {
    overflow: hidden !important;
}
.document-error {
    font-size: 16px;
    text-align: center;
    margin-top: 140px;
    width: 100%;
    display: none;
}
.document-error i {
    font-size: 46px;
}
.document-pagination button {
        border: none;
    height: 30px;
    width: 29px;
    padding: 6px 12px !important;
}

#document-viewer {
    width: auto;
    border: 1px solid #fff;
}
.datatable-wrap {
    overflow-x: auto;
    min-height: 400px;
}
.note-editor ol, .note-editor ul, .bq-note-text ol, .bq-note-text ul {
    list-style: revert;
    margin: revert;
    padding: revert;
}

.record-booking-mobile {
    display:none;
}
.card-content {
    max-width: 100%;
}
.grand-total, .tax-total, .sub-total {
    display: inline-block;
    width: 190px;
}

@media (max-width: 470px) {
    .record-booking-desktop {
        display:none;
    }
    .record-booking-mobile {
        display: inline-flex;
    }
}


/*signature*/
.draw-signature-holder {
    width: 330px;
    border: 2px solid #e6eaee;
    border-radius: 5px;
    background-color: #fff !important;
    margin: 0 auto;
}
canvas#draw-signature {
    border-radius: 5px;
    background-color: transparent !important;
    cursor: pointer;
}
.signature-tool-item {
    width: 40px;
    height: 30px;
    padding-top: 6px;
    overflow: hidden;
}
.signer-tool, .signature-tool-item {
    text-align: center;
    font-size: 12px;
    display: inline-block;
    cursor: pointer;
    min-width: 50px;
    margin: 0 6px;
}
button.jscolor {
    border-radius: 50%;
    width: 20px;
    height: 20px;
    margin: 0 0 0 0 !important;
    padding: 0;
    border: 0 !important;
}

.signature-tool-item .tool-icon {
    width: 20px;
    height: 20px;
    display: inline-block;
    margin-bottom: 4px;
}
.signature-tool-item .tool-icon.tool-stroke {
    background-color: #7f8fa4;
    -webkit-mask: url(../images/stroke.svg) no-repeat center;
    mask: url(../images/stroke.svg) no-repeat center;
    -webkit-mask-repeat: no-repeat;
    -webkit-mask-size: contain;
}
.signature-tool-item .tool-icon.tool-undo {
    background-color: #7f8fa4;
    -webkit-mask: url(../images/undo.svg) no-repeat center;
    mask: url(../images/undo.svg) no-repeat center;
    -webkit-mask-repeat: no-repeat;
    -webkit-mask-size: contain;
}
.signature-tool-item .tool-icon.tool-erase {
    background-color: #7f8fa4;
    -webkit-mask: url(../images/erase.svg) no-repeat center;
    mask: url(../images/erase.svg) no-repeat center;
    -webkit-mask-repeat: no-repeat;
    -webkit-mask-size: contain;
}

@media (max-width: 500px) {

    .draw-signature-holder {
        width: 260px;
    }
}

</style>
      
      
     <section class="container">
        <div class="form-group">
                        <div class="car-diagram-holder">
                            <canvas id="car-diagram" style="width: 596px; height: 298px;" width="596" height="298"></canvas></div>
                        <input type="hidden" name="car_diagram" value="">
                        <div class="signature-tools text-center" id="controls">
                            <div class="signature-tool-item with-picker">
                                <div><div class="dent-scratch-color red" color-label=".color-red" color-code="#ff0000"></div></div>
                            </div>
                            <div class="signature-tool-item with-picker">
                                <div><div class="dent-scratch-color blue active" color-label=".color-blue" color-code="#1418FF"></div></div>
                            </div>
                            <div class="signature-tool-item" id="undo">
                                <div class="tool-icon tool-undo"></div>
                            </div>
                            <div class="nk-divider divider mt-2 mb-2"></div>
                            <p class="form-note mb-1 selected-label color-red" style="display: none;"><em class="icon ni ni-circle-fill" style="color:#ff0000;"></em> <span class="text-muted">Dents marking selected</span></p>
                            <p class="form-note mb-1 selected-label color-blue" style=""><em class="icon ni ni-circle-fill" style="color:#1418FF;"></em> <span class="text-muted">Scratch marking selected</span></p>
                        </div>
                    </div>
    </section>
        <script  type="text/javascript">
    
    
var modules = {};
function initMarking(){
    ! function(a) {
        a(document).ready(function() {
            function o(a) {
                return void 0 !== window.ontouchstart && u[a] && (a = u[a]), a
            }

            function e(a) {
                return a.originalEvent.changedTouches ? {
                    pageX: a.originalEvent.changedTouches[0].pageX,
                    pageY: a.originalEvent.changedTouches[0].pageY
                } : {
                    pageX: a.pageX,
                    pageY: a.pageY
                }
            }

            function n() {
                l.$canvas.drawRect({
                    fillStyle: "transparent",
                    x: 0,
                    y: 0,
                    width: l.canvasW,
                    height: l.canvasH,
                    fromCenter: !1
                })
            }

            function s(a) {
                l.$canvas.clearCanvas(), l.$canvas.drawImage({
                    source: a,
                    x: 0,
                    y: 0,
                    width: l.canvasW,
                    height: l.canvasH,
                    fromCenter: !1
                })
            }

            modules.diagramcolor = function c(color) {
                l.color = color
            }

            function r(a) {
                var o = d.slider.width();
                d.slider.children("#filler").width(o * (a / 100))
            }
            var i, l = {
                    $canvas: a("#car-diagram"),
                    color: "#ff0000",
                    press: !1,
                    last: new Image,
                    hist: [],
                    undoHist: [],
                    clicks: 0,
                    start: !1
                },
                d = {
                    box: a("#box"),
                    tools: a("#tools"),
                    clear: a("#clear"),
                    slider: a("#slider"),
                    undo: a("#undo")
                },
                u = {
                    mousedown: "touchstart",
                    mouseup: "touchend",
                    mousemove: "touchmove"
                };
            l.getTouchEventName = o, l.getPageCoords = e, l.clearCanvas = n;
            d.clear.on("click", function() {
                    l.$canvas.trigger("mouseup"), l.last.src = l.$canvas[0].toDataURL("image/png"), l.hist.push(l.last.src), n(), l.clicks = 0, l.$canvas.clearCanvas()
                }), d.undo.on("click", function() {
                    l.$canvas.mouseup(), l.hist.length > 0 && (l.clicks = 0, l.undoHist.push(l.$canvas[0].toDataURL("image/png")), s(l.hist.pop()))
                }), l.$canvas.brushTool(l), modules.diagramcolor(l.color), d.slider.slider({
                    min: 1,
                    value: 50
                });
            var p = d.slider.slider("option", "value");
            r(p), l.stroke = 5, modules.stroke = function updateStroke(width) {
                    l.stroke = width
                },
                function() {
                    var a = l.$canvas.getCanvasImage("image/png");
                    l.canvasW = l.$canvas.attr("width"), l.canvasH = l.$canvas.attr("height"), l.$canvas.prop({
                        width: l.canvasW,
                        height: l.canvasH
                    }), l.$canvas.detectPixelRatio(), a.length > 10 && l.$canvas.drawImage({
                        source: a,
                        x: 0,
                        y: 0,
                        width: l.canvasW,
                        height: l.canvasH,
                        fromCenter: !1
                    })
                }(), n()
        })
    }(jQuery);
    ! function(e) {
        e.fn.brushTool = function(e) {
            var t = this,
            tOffset = t.offset();
            t.unbind(), e.clicks = 0;
            var o, n, a, r, s = function() {
                t.drawLine({
                    strokeWidth: e.stroke,
                    strokeStyle: e.color,
                    rounded: true,
                    strokeCap: "round",
                    strokeJoin: "round",
                    x1: o,
                    y1: n,
                    x2: a,
                    y2: r
                })
            };
            t.on("mousedown touchstart", function(s) {
                if (e.hist.push(e.last.src = t[0].toDataURL("image/png")), e.undoHist.length = 0, !0 === e.press && (e.clicks = 0), 0 === e.clicks) {
                    e.drag = !0;
                    var c = e.getPageCoords(s);
                    o = c.pageX - tOffset.left, n = c.pageY - tOffset.top, a = o, r = n, t.drawArc({
                        fillStyle: e.color,
                        x: o,
                        y: n,
                        radius: e.stroke / 2,
                        start: 0,
                        end: 360
                    }), e.clicks += 1
                }
                s.preventDefault()
            }), t.on("mouseup touchend", function(o) {
                e.drag = !1, e.last.src = t[0].toDataURL("image/png"), e.clicks = 0, o.preventDefault()
            }), t.on("mousemove touchmove", function(t) {
                if (!0 === e.drag && e.clicks >= 1) {
                    o = a, n = r;
                    var c = e.getPageCoords(t);
                    a = c.pageX - tOffset.left, r = c.pageY - tOffset.top, s()
                }
                t.preventDefault()
            })
        }
    }(jQuery);
}



    </script>
        