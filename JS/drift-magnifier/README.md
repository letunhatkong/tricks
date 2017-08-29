
<a name="installation"></a>
## Installation

* **NPM**: `npm install drift-zoom`
* **Bower**: `bower install drift`
* **Manual**: [Download](https://github.com/imgix/drift/archive/master.zip) and use `dist/Drift.min.js` or `dist/Drift.js`

If you're using the pre-built version of Drift, it will automatically make `window.Drift` available for your use when included on your page.

If you prefer to use `require` statements and a build tool like Browserify, here are a couple examples to help:

 ``` javascript
var Drift = require('drift-zoom');

new Drift(…);
```

If your project uses ES6, you can do the following instead:

``` javascript
import Drift from 'drift-zoom';

new Drift(…);
```


<a name="basic-usage"></a>
## Basic Usage

Once you've installed Drift via one of the above methods, you're ready to get started. There are no dependencies, so you can just start making cool stuff. Check out the [announcement blog post](http://blog.imgix.com/2016/01/06/better-lightbox-zoom-viewer-with-imgix.html) for a demo, or take a peek here: https://imgix.github.io/drift. Here's an example of the most basic possible implementation:

``` html
<img src="http://assets.imgix.net/dog.png?w=400" data-zoom="http://assets.imgix.net/dog.png?w=1200">

<p>This is a simple description of the dog picture.</p>
```

``` javascript
new Drift(document.querySelector('img'), {
  paneContainer: document.querySelector('p')
});
```


<a name="options-defaults"></a>
## Options / Defaults

Here's an example of using Drift with a custom configuration. All of the listed options are displayed with their default value.

``` javascript
var options = {
	// Prefix for generated element class names (e.g. `my-ns` will
	// result in classes such as `my-ns-pane`. Default `drift-`
	// prefixed classes will always be added as well.
	namespace: null,
	// Whether the ZoomPane should show whitespace when near the edges.
	showWhitespaceAtEdges: false,
	// Whether the inline ZoomPane should stay inside
	// the bounds of its image.
	containInline: false,
	// How much to offset the ZoomPane from the
	// interaction point when inline.
	inlineOffsetX: 0,
	inlineOffsetY: 0,
	// A DOM element to append the inline ZoomPane to.
	inlineContainer: document.body,
	// Which trigger attribute to pull the ZoomPane image source from.
	sourceAttribute: 'data-zoom',
	// How much to magnify the trigger by in the ZoomPane.
	// (e.g., `zoomFactor: 3` will result in a 900 px wide ZoomPane image
	// if the trigger is displayed at 300 px wide)
	zoomFactor: 3,
	// A DOM element to append the non-inline ZoomPane to.
	// Required if `inlinePane !== true`.
	paneContainer: document.body,
	// When to switch to an inline ZoomPane. This can be a boolean or
	// an integer. If `true`, the ZoomPane will always be inline,
	// if `false`, it will switch to inline when `windowWidth <= inlinePane`
	inlinePane: 375,
	// If `true`, touch events will trigger the zoom, like mouse events.
	handleTouch: true,
	// If present (and a function), this will be called
	// whenever the ZoomPane is shown.
	onShow: null,
	// If present (and a function), this will be called
	// whenever the ZoomPane is hidden.
	onHide: null,
	// Add base styles to the page. See the "Theming"
	// section of README.md for more information.
	injectBaseStyles: true,
	// An optional number that determines how long to wait before
	// showing the ZoomPane because of a `mouseenter` event.
	hoverDelay: 0,
	// An optional number that determines how long to wait before
	// showing the ZoomPane because of a `touchstart` event.
	// It's unlikely that you would want to use this option, since
	// "tap and hold" is much more intentional than a hover event.
	touchDelay: 0,
	// If true, a bounding box will show the area currently being previewed
	// during mouse hover
	hoverBoundingBox: false,
	// If true, a bounding box will show the area currently being previewed
	// during touch events
	touchBoundingBox: false,
};

new Drift(document.querySelector('img'), options);
```


<a name="api"></a>
## API

### `Drift#disable`

Disable your Drift instance. This will prevent your Drift instance from showing, but will not hide it if it's currently visible.

``` javascript
var drift = new Drift(document.querySelector('img'), {
  paneContainer: document.querySelector('p')
});

document.querySelector('.disable-button').addEventListener('click', function() {
  drift.disable();
});
```

### `Drift#enable`

Enable your Drift instance.

``` javascript
var drift = new Drift(document.querySelector('img'), {
  paneContainer: document.querySelector('p')
});

document.querySelector('.enable-button').addEventListener('click', function() {
  drift.enable();
});
```

### `Drift#setZoomImageURL(imageURL)`

Change the URL of the zoom image to the passed string. This only has a visible effect while your Drift is currently open. When opening, Drift always pulls the zoom image URL from the specified `sourceAttribute`. If you want to make a "permanent" change that will persist after the user leaves and re-enters your Drift trigger, you update its `sourceAttribute` as well (default `data-zoom`). For more information about this method, please see [issue #42](https://github.com/imgix/drift/issues/42).

``` javascript
var triggerEl = document.querySelector('img');
var drift = new Drift(triggerEl, {
  paneContainer: document.querySelector('p')
});

var frames = [
  'https://mysite.com/frame1.jpg',
  'https://mysite.com/frame2.jpg',
  'https://mysite.com/frame3.jpg'
];

var currentFrame = 0;

setInterval(function() {
  currentFrame++

  if (currentFrame > frames.length - 1) {
    currentFrame = 0;
  }

  drift.setZoomImageURL(frames[currentFrame]);
  triggerEl.setAttribute('data-zoom', frames[currentFrame]);
}, 1200);
```



