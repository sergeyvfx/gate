/**
 * Gate - Wiki engine and web-interface for WebTester Server
 *
 * Cross-browser drag-n-dropping anchors
 *
 * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
 *
 * This program can be distributed under the terms of the GNU GPL.
 * See the file COPYING.
 */

var anchor_draggingElement = null;
var anchor_RelativeX = 0, anchor_RelativeY = 0;
var anchor_Registered = new Array ();
var anchor_Hover = null;
var anchor_StartScroll = {scrollX:0, scrollY:0};
var anchor_StartMouseCoords = {x:0, y:0};
var anchor_StartCoords = {x:0, y:0};
var anchor_Deletion = null,
    anchor_Src = null,
    anchor_Dst = null,
    anchor_Callback = null;

function anchor_SetStartCoords (x,y)  { anchor_StartCoords = {x:x, y:y}; }
function anchor_GetStartCoords ()     { return anchor_StartCoords; }
function anchor_SetStartScroll (x,y)  { anchor_StartScroll = {scrollX:x, scrollY:y}; }
function anchor_GetStartScroll ()     { return anchor_StartScroll; }
function anchor_SetStartMouseCoords (x,y)  { anchor_StartMouseCoords = {x:x, y:y}; }
function anchor_GetStartMouseCoords ()     { return anchor_StartMouseCoords; }
function anchor_SetHover (self) { anchor_Hover = self; }
function anchor_GetHover ()     { return anchor_Hover; }

function anchor_SpawnNewDragging (source) {
  var newElement = document.createElement ('IMG');
  var pos = core_ElementPosition (source);
  newElement.className = "anchor dragging";
  newElement.style.position = 'absolute';
  newElement.style.left = pos.x + 'px';
  newElement.style.top  = pos.y + 'px';
  newElement.src=source.src;
  newElement.ondragstart = function () { return false; }
  newElement.onmousedown = function () { return false; }
  document.body.appendChild (newElement);
  return newElement;
}

function anchor_RemoveDragging () {
  if (anchor_draggingElement) {
    // Sucky IE have really bugs here
    anchor_Deletion = anchor_draggingElement.node;
    anchor_Deletion.className = "";
    setTimeout ("document.body.removeChild (anchor_Deletion);", 10);
    delete anchor_draggingElement.node;
    anchor_draggingElement.node = null;
  }
}

function anchor_SetDragging (self, id) {
  if (!self) {
    anchor_RemoveDragging ();
    anchor_draggingElement = null;
    anchor_SetHover (null);
    return;
  }
  anchor_draggingElement = {node:anchor_SpawnNewDragging (self), id:id};
}
function anchor_GetDragging ()  { return anchor_draggingElement; }

function anchor_GetRelativeX () { return anchor_RelativeX; }
function anchor_GetRelativeY () { return anchor_RelativeY; }

function anchor_SetRelativeCoords (x, y) {
  anchor_RelativeX = x;
  anchor_RelativeY = y;
}

function anchor_StoreRelativeCoords (element, event) {
  var x, y;
  x = event.clientX - element.x;
  y = event.clientY - element.y;
  anchor_SetRelativeCoords (x, y);
}

function anchor_UpdateHover (x, y, id) {
  anchor_SetHover (null);
  var scroll = core_GetPageScroll ();
  for (var i = 0; i < anchor_Registered.length; i++) { 
    var anchor = anchor_Registered[i];
    if (anchor.id != id &&
        core_PointInRectangle (x + scroll.scrollX, y + scroll.scrollY, anchor.rec)) {
      anchor.node.className = 'anchor hover';
      anchor_SetHover (anchor);
    } else {
      anchor.node.className = 'anchor';
    }
  }
}

function anchor_UpdateDraggingPosition (event) {
  var dragging = anchor_GetDragging ();

  if (dragging.rollback) {
    return;
  }

  var node = dragging.node;
  var scroll = core_GetPageScroll ();
  var startScroll = anchor_GetStartScroll ();

  anchor_UpdateHover (event.clientX, event.clientY, dragging.id);

  var dx = scroll.scrollX - startScroll.scrollX,
      dy = scroll.scrollY - startScroll.scrollY;
  var sCoords = anchor_GetStartMouseCoords ();
  var cdx = event.clientX - sCoords.x,
      cdy = event.clientY - sCoords.y;
  var x = parseInt (node.style.left + 0), y = parseInt (node.style.top + 0);

  x += cdx + dx;
  y += cdy + dy;

  node.style.left = x + 'px';
  node.style.top  = y + 'px';

  anchor_StoreStartScroll ();
  anchor_SetStartMouseCoords (event.clientX, event.clientY);
}

function anchor_StoreStartMouseCoords (event) { anchor_SetStartMouseCoords (event.clientX, event.clientY); }
function anchor_StoreStartScroll ()           { anchor_SetStartScroll (core_GetPageScroll ().scrollX, core_GetPageScroll ().scrollY); }
function anchor_StoreStartCoords (element)    { var pos = core_ElementPosition (element); anchor_SetStartCoords (pos.x, pos.y); }

function anchor_StartDrag (element, id, event) {
  anchor_SetHover (null);
  anchor_StoreStartScroll ();
  anchor_StoreRelativeCoords (element, event);
  anchor_StoreStartMouseCoords (event);
  anchor_StoreStartCoords (element);
  anchor_SetDragging (element, id);
}

function anchor_CallCallback (source, destination) {
  if (source == destination) {
    return;
  }

  for (var i = 0; i < anchor_Registered.length; i++) {
    var anchor = anchor_Registered[i];
    if (anchor.id == source) {
      anchor_SetDragging (null, -1);
      anchor_UpdateHover (-1, -1, -1);
      anchor_Src = source;
      anchor_Dst = destination;
      anchor_Callback = anchor.callback;
      setTimeout ("anchor_Callback (anchor_Src, anchor_Dst);", 10);
      return;
    }
  }
}

function anchor_StopDrag () {
  var hover;

  if (hover = anchor_GetHover ()) {
    var element = anchor_GetDragging ();
    anchor_CallCallback (element.id, hover.id);
  }

  if (anchor_GetDragging ()) {
    anchor_SetDragging (null, -1);
    anchor_UpdateHover (-1, -1, -1);
  }
}

function anchor_ProceedDrag (event) {
  if (anchor_GetDragging ()) {
    anchor_UpdateDraggingPosition (event);
  }
}

function anchor_Register (id, callback) {
  var node = getElementById ('anchor_' + id);
  anchor_Registered[anchor_Registered.length] = {node:node, id:id, callback:callback,
    rec:core_ElementRectangle (node)};
}

////
// Event handlers
function anchor_OnMouseDown (element, id, event) { anchor_StartDrag (element, id, event);  }
function anchor_OnMouseUp   ()                   { anchor_StopDrag (); }
function anchor_OnMouseMove (event)              { anchor_ProceedDrag (event); }
function anchor_OnPageScroll (event) {
  var coords = core_GetMouseCoords ();
  anchor_ProceedDrag ({clientX:coords.x, clientY:coords.y});
}
