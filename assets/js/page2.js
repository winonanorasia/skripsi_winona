
function getListPeriode(tahun,j){
    let dropdown = null;
    if(j==1){
        dropdown = $('#periode1');
    }else{
        dropdown = $('#periode2');
    }
    dropdown.empty();
    $.get('getBulan/'+tahun, function(data){
        $.each(data, function (key, entry) {
            dropdown.append($('<option></option>').attr('value', entry.id).text(entry.ket));
        });
    });
};

function validateForm() {
    var a = document.forms["formPred"]["alpha"].value;
    var b = document.forms["formPred"]["beta"].value;
    var g = document.forms["formPred"]["gamma"].value;

    var n = document.forms["formPred"]["jumlah_n"].value;

    var th1 = document.forms["formPred"]["thnMulai"].value;
    var prd1 = document.forms["formPred"]["prdMulai"].value;
    
    var th2 = document.forms["formPred"]["thnSampai"].value;
    var prd2 = document.forms["formPred"]["prdSampai"].value;
    
    var jmlf = document.forms["formPred"]["jumlah_folds"].value;

    if(a=='' || b=='' || g==''){
        alert('alpha, beta , gamma harus diisi!');
        return false;
    }
    if(n==''){
        alert('jumlah peramalan (n) harus diisi!');
        return false;
    }
    if(th1=='' || prd1==''){
        alert('Tahun Mulai dan Periode Mulai harus diisi!');
        return false;
    }
    if(th2=='' || prd2==''){
        alert('Tahun Sampai dan Periode Sampai harus diisi!');
        return false;
    }

    if(th1 > th2){
        alert('Tahun Sampai tidak boleh lebih kecil dari tahun mulai!');
        return false;
    }

    if(parseInt(prd2) < (parseInt(n) * 2) ){
        alert('Jumlah n lebih dari jumlah periode!');
        return false;
    }

    if(typeof jmlf !== 'undefined'){
        if(jmlf == ''){
            alert('Jumlah KFold harus diisi!');
            return false;
        }
    }
};

$(function() {
    let datagraph = [];
    if(typeof id_predik !== 'undefined'){
        $.get(url+'prediksi/getGraph/'+id_predik, function(data){
            $.each(data, function (key, entry) {
                datagraph.push({
                    y:entry[1] + '-' + entry [2],
                    a:entry[3] == '' ? 0 : entry[3],
                    b:entry[10] == ''? 0 : entry[10]
                });
            });
            
            Morris.Line({
                element: 'line_chart',
                data: datagraph,
                parseTime: false,
                gridTextSize: 9,
                xkey: 'y',
                ykeys: ['a', 'b'],
                labels: ['Aktual', 'Forecast'],
                hideHover: 'auto',
                ymax : 'auto',
                resize: 'true',
                lineColors: ['#52a7e0','#ee4f00'],
            });
        });
    }

    if(typeof id_uji !== 'undefined'){
        $.get(url+'pengujian/getGraph/'+id_uji, function(data){
            $.each(data, function (key, entry) {
                datagraph.push({
                    y:entry[1] + '-' + entry [2],
                    a:entry[3] == '' ? 0 : entry[3],
                    b:entry[10] == ''? 0 : entry[10]
                });
            });
            
            Morris.Line({
                element: 'line_chart',
                data: datagraph,
                parseTime: false,
                gridTextSize: 9,
                xkey: 'y',
                ykeys: ['a', 'b'],
                labels: ['Aktual', 'Forecast'],
                hideHover: 'auto',
                ymax : 'auto',
                resize: 'true',
                lineColors: ['#52a7e0','#ee4f00'],
            });
        });
    }
});