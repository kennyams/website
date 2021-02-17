var scale=10000;

function normalize(outline,m){
    let w = (m.maxx - m.minx)/100;                     
    let h = (m.maxy - m.miny)/75;                      
    outline.forEach(function(c){                       
        c.x=(c.x-m.minx)/w;                            
        c.y=(c.y-m.miny)/h;                            
    });
}

function minmax(outlines){
    let minx=180 * scale;
    let miny=90 * scale;
    let maxx=-180 * scale;
    let maxy=-90 * scale;
    outlines.forEach(function(o){                
	//	console.log(o);
        o.forEach(function(c){                   
            minx=(c.x<minx?c.x:minx);                  
            miny=(c.y<miny?c.y:miny);                  
            maxx=(c.x>maxx?c.x:maxx);
            maxy=(c.y>maxy?c.y:maxy);                  
        });                                            
    });
    return {minx:minx,miny:miny,maxx:maxx,maxy:maxy};

};

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

