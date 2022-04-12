var scale=1;

function normalize(outline,m,x,y){
    let w = (m.maxx - m.minx)/x;
    let h = (m.maxy - m.miny)/y;
    outline.forEach(function(c){
        c.x=(c.lon-m.minx)/w;
        c.y=(c.lat-m.miny)/h;
		c.y=y-c.y;
    });
}

function minmax(outlines){
    let minx=180 * scale;
    let miny=90 * scale;
    let maxx=-180 * scale;
    let maxy=-90 * scale;
    outlines.forEach(function(o){
        o.forEach(function(c){
            minx=(c.lon<minx?c.lon:minx);
            miny=(c.lat<miny?c.lat:miny);
            maxx=(c.lon>maxx?c.lon:maxx);
            maxy=(c.lat>maxy?c.lat:maxy);
        });
    });
    return {minx:minx,miny:miny,maxx:maxx,maxy:maxy};

}

function createRoute(w){
	var outline = w.path;
	var id=w.id;
	var name=w.name;
	//console.log("createRoute");
	//var g = makeSVG("g",{});
	var t = makeSVG("text",{"id":`t_${id}`});
	var tp = makeSVG("textPath",{"id":`tp_${id}`, "href":`#${id}`,"side":"left","method":"stretch","spacing":"auto","startOffset":"0%"});
	tp.innerHTML=name;
	//g.appendChild(t);
	t.appendChild(tp);
	//console.log(t);
	return t;

}

function createPath(outline){
    var first=true;
    var d="";
    outline.forEach(function(c){
        d=d.concat(first?"M" + c.x + " " + c.y:" L" + c.x + " " + c.y);
        first=false;
    });
    return d;
}

function makeSVG(tag, attrs) {
    var el= document.createElementNS('http://www.w3.org/2000/svg', tag);
    for (var k in attrs){
        el.setAttribute(k, attrs[k]);
    }
    return el;
}

class Location{
	constructor(p,callback){
		var p1,p2;
		$(p).submit(function(event){
			event.preventDefault();
			place=$(p+" input").val();
			$.get("https://nominatim.openstreetmap.org/search",{q:place,format:"json"},function(data){
				var boxcoords = data[0].boundingbox
				//console.log(boxcoords);
				var p1 =[boxcoords[0],boxcoords[2]];
				var p2 =[boxcoords[1],boxcoords[3]];
				callback([p1,p2]);
			});
		});
	}
	getLocation(textbox_id){
		return [p1,p2];
	}
}
