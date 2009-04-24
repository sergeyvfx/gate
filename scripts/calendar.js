/**
 * Gate - Wiki engine and web-interface for WebTester Server
 *
 * JavaScript for calendar
 *
 * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
 *
 * This program can be distributed under the terms of the GNU GPL.
 * See the file COPYING.
 */

// ==STUFF==
var CDMonth  = [31,28,31,30,31,30,31,31,30,31,30,31];
var VCDMonth = [31,29,31,30,31,30,31,31,30,31,30,31];
var Moths    = ['Января','Февраля','Марта','Апреля','Мая','Июня','Июля','Августа',
                'Сентября','Октября','Ноября','Декабря'];

function get_cdays_of_month (month, year) {
  if ((year % 4 == 0 && year % 100 != 0) || (year % 400 == 0)) {
    return VCDMonth[month - 1];
  }

  return CDMonth[month-1];
}

function get_first_day_of_month (month, year) {
  var cMon = 1, cYear = 1972, cDay = 6;

  if (year < 1972) {
    alert ('Слишком старый год!');
    return 0;
  }

  while (cYear != year || (cYear == year && cMon < month)) {
    cDay = (cDay + get_cdays_of_month (cMon, cYear)) % 7;
    if (cDay == 0) {
      cDay = 7;
    }

    cMon++;

    if (cMon == 13)  {
      cMon=1;
      cYear++;
    }
  }

  return cDay;
}


function calendar_Init (name) {
  clanedar_InitDays (name);
  calendar_updateTitle (name);
}

function clanedar_InitDays (name) {
  var days = calendar_getElementById (name, 'days');

  if (!days) {
    return;
  }

  for (var i = 0; i < 5; i++) {
    var tr = document.createElement ('tr');
    for (var j = 0; j < 7; j++) {
      var td = document.createElement ('td');
      td.innerHTML = '1';
      td.id = name + '_dayCell_' + i + '_' + j;

      if (j >= 5) {
        td.className='h';
      }

      tr.appendChild (td);
    }

    if (isMSIE)
      days.firstChild.appendChild (tr); else
      days.appendChild (tr);
  }

  calendar_updateDays (name);
}

function calendar_updateDays (name) {
  var date = calendar_getCurrentDate (name);
  var day, act = 0, day2;
  var m = atoi (date.m), y = atoi (date.y), d = atoi (date.d);
  var pm = m - 1, py = y;

  if (pm < 1) {
    pm=1;
    py--;
  }

  var n = get_cdays_of_month (m, y),
      f = get_first_day_of_month (m, y);
  var pn = get_cdays_of_month (pm, py);

  day = pn - f + 1;
  act = 0;

  for (var i = 0; i < 5; i++)
    for (var j = 0; j < 7; j++) {
      var id = name + '_dayCell_' + i + '_' + j;
      var node = getElementById (id);

      node.className = '';

      if (act == 0 && day >= pn)  {
        act = 1;
        day = 0;
      }

      if (act == 1 && day >= n) {
        act = 2;
        day = 0;
      }

      day++;

      if (act == 0 || act == 2) {
        node.innerHTML = day;
        node.className = 'd';
      } else {
        day2 = day;

        if (day2 < 10) {
          day2 = '0' + day2;
        }

        node.innerHTML = '<a href="JavaScript:calendar_onDayChange (\'' + name + '\', \'' + day2 + '\');">' + day + '</a>';
        node.tag = day;

        if (day == d) {
          node.className = 'c';
        }
      }

      if (j >= 5) {
        node.className += ' h';
      }
    }
}

function calendar_getElementById (name, id) {
  var node = getElementById ('calendar_' + name);

  if (!node) {
    return;
  }

  return elementByIdInTree (node, id);
}

function calendar_getCurrentDate (name) {
  var el = getElementById (name);

  if (!el) {
    return;
  }

  var date = el.value;
  var d = date.replace (/^([0-9]+)\-([0-9]+)\-([0-9]+)$/i, '$3');
  var m = date.replace (/^([0-9]+)\-([0-9]+)\-([0-9]+)$/i, '$2');
  var y = date.replace (/^([0-9]+)\-([0-9]+)\-([0-9]+)$/i, '$1');

  return {d:d, m:m, y:y}
}

function calendar_updateTitle (name) {
  var date = calendar_getCurrentDate (name);
  var node = calendar_getElementById (name, 'title');

  if (!date) {
    return;
  }

  node.value = date.d + ' ' + Moths[atoi (date.m) - 1] + ' ' + date.y + ' года';
}

function calendar_setCurrentDate (name, date) {
  var s = date.y + '-' + date.m + '-' + date.d;
  getElementById (name).value = s;
  calendar_updateTitle (name);
}

function calendar_updateYears (name, year) {
  var node = calendar_getElementById (name, 'year');
  var nodes = node.childNodes;

  // Remove old years
  node.value = '';

  var y = atoi (year) - 2;

  for (var i = 0; i < nodes.length; i++) {
    var item = nodes.item (i);
    if (item.tagName && item.tagName.toLowerCase () == 'option') {
      item.value = y;
      item.innerHTML = y;
      y++;
    }
  }
  node.value = year;
}

function calendar_updateYear (name, year)   {
  var cDate = calendar_getCurrentDate (name);
  cDate.y = year;
  calendar_setCurrentDate (name, cDate);
}

function calendar_updateMonth (name, month) {
  var cDate = calendar_getCurrentDate (name);
  cDate.m = month;
  calendar_setCurrentDate (name, cDate);
}

function calendar_onYearChange (name) {
  var year = calendar_getElementById (name, 'year').value;

  calendar_updateYears (name, year);
  calendar_updateYear (name, year);
  calendar_updateDays (name);
}

function calendar_onMonthChange (name) {
  var month = calendar_getElementById (name, 'month').value;

  calendar_updateMonth (name, month);
  calendar_updateDays (name);
}

function calendar_onDayChange (name, day) {
  var date = calendar_getCurrentDate (name);
  var d = atoi (date.d);
  var changed;

  for (var i = 0; i < 5; i++)
    for (var j = 0; j < 7; j++) {
      var node = getElementById (name + '_dayCell_' + i + '_' + j);
      changed = false;
      if (node.className != 'd' && node.className != 'd h') {
        if (node.tag == d)   { node.className = '';  changed = true; }
        if (node.tag == day) { node.className = 'c'; changed = true; }
        if (changed && j >= 5) node.className += ' h';
      }
    }

  date.d = day;
  calendar_setCurrentDate (name, date);
  calendar_changeFull (name);
}

function calendar_changeFull (name) {
  var _short = calendar_getElementById (name, 'short');
  var _full = calendar_getElementById (name, 'full');

  if (_full.style.display) {
    _short.style.display = 'none';
    _full.style.display  = '';
  } else {
    _short.style.display = '';
    _full.style.display  = 'none';
  }
}
