<script>
// initialize SVG.js
var draw_1 = SVG().addTo('.svg-container')
$("svg").attr({
	version: "1.1",
	viewBox: "0 0 500 500",
	preserveAspectRatio: "xMinYMin meet"
});

// draw pink square
var circle_1 = draw.circle(20).move(0, 50)
</script>