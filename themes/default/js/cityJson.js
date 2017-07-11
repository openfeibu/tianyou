province = area = {};
$.getJSON("js/province_json.js",function(data){
    province = data;
});
$.getJSON("js/area_json.js",function(data){
    area = data;
});
