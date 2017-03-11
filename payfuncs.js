//   Copyright 2012 John Collins

//   This program is free software: you can redistribute it and/or modify
//   it under the terms of the GNU General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or
//   (at your option) any later version.

//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.

//   You should have received a copy of the GNU General Public License
//   along with this program.  If not, see <http://www.gnu.org/licenses/>.

function replacecell(tab, row, val, hl)  {
	var trow = tab.rows[row];
	var tcell = trow.cells[1];
	var vnode;
	if (hl)  {
		vnode = document.createElement('span');
		vnode.innerHTML = val;
	}
	else
		vnode = document.createTextNode(val);
	tcell.replaceChild(vnode, tcell.firstChild);
}	

function fillinvals() {
	var vform = document.payform;
	var asl = vform.actselect;
	var ind = asl.selectedIndex;
	if (ind < 0)
		return;
	var str = asl.options[ind].value;
	var pieces = str.split(':');
	var pftab = document.getElementById('pftab');
	var typev,namev,bgav,totv;
	if (pieces.length == 4) {
		typev = "Team &pound;15";
		namev = pieces[1];
		var nm = parseInt(pieces[2]);
		if (nm == 0)
			bgav = "All BGA members";
		else if (nm == 1)
			bgav = "One non-BGA member &pound;5";
		else  {
			surch = 5 * nm;
			bgav = nm + " non-BGA members @ &pound;5 per non-member - &pound;" + surch;
		}
		totv = pieces[3];			
	}
	else {
		typev = "Individual &pound;10";
		namev = pieces[1] + ' ' + pieces[2];
		if (parseInt(pieces[3]) != 0)
			bgav = "Not BGA member &pound;5";
		else
			bgav = "None";
		totv = pieces[4];
	}
	replacecell(pftab, 1, typev, 1);
	replacecell(pftab, 2, namev, 0);
	replacecell(pftab, 3, bgav, 1);
	replacecell(pftab, 4, "&pound;" + totv, 1);
	vform.amount.value = totv;
}