<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Treinamento Philips</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{font-family:Arial;margin:20px;background:#fff;}
h1{background:#0f5c74;color:#fff;padding:15px;border-radius:8px;}
.subinfo{margin:15px 0;}
table{width:100%;border-collapse:collapse;font-size:13px;}
th{background:#1f6f8b;color:#fff;padding:6px;border:1px solid #ccc;}
td{border:1px solid #ddd;padding:6px;}
td[contenteditable]:focus{outline:2px solid #1f6f8b;}
tr:nth-child(even){background:#f2f2f2;}
.dashboard{margin-top:25px;padding:15px;border:1px solid #ccc;border-radius:8px;background:#f4f6f9;}
.progress-bar{height:20px;background:#ddd;border-radius:4px;overflow:hidden;margin-top:8px;}
.progress-fill{height:100%;width:0%;background:#28a745;}
button{padding:8px 15px;background:#007bff;color:white;border:none;border-radius:6px;cursor:pointer;margin-right:5px;}
button:hover{opacity:0.9;}
</style>
</head>

<body>

<h1 contenteditable>Treinamento Philips</h1>

<div class="subinfo">
<strong contenteditable>Vivere Consultoria</strong><br><br>
Responsável: <span contenteditable>André Soares</span>
</div>

<button onclick="salvar()">💾 Salvar</button>
<button onclick="novaLinha()">➕ Nova Linha</button>

<table id="tabela">
<thead>
<tr>
<th>Descrição</th>
<th>Progresso (%)</th>
<th>Observação</th>
<th>Ação</th>
</tr>
</thead>
<tbody>
</tbody>
</table>

<div class="dashboard">
<strong>Progresso: </strong><span id="media"></span><br>
Total: <span id="total"></span><br>
<div class="progress-bar">
<div class="progress-fill" id="barra"></div>
</div>
</div>


<script>

const URL_SCRIPT = "https://script.google.com/macros/s/AKfycbxpYpq64MHzA9UaDSF0tYE5EdF9H-t28tIhRN5m0Iy6t-Ucnecb6sCa7fOIpzamjN0RFg/exec";

let chart;

function adicionarLinha(dados={descricao:"",progresso:0,observacao:""}){
    let tr=document.createElement("tr");
    tr.innerHTML=`
    <td contenteditable>${dados.descricao}</td>
    <td contenteditable>${dados.progresso}</td>
    <td contenteditable>${dados.observacao}</td>
    <td><button onclick="this.closest('tr').remove(); calcular();">❌</button></td>
    `;
    document.querySelector("#tabela tbody").appendChild(tr);
}

function novaLinha(){
    adicionarLinha();
}

function calcular(){
    let linhas=document.querySelectorAll("#tabela tbody tr");
    let total=linhas.length;
    let soma=0;

    linhas.forEach(l=>{
        let prog=parseFloat(l.cells[1].innerText)||0;
        if(prog>100) prog=100;
        if(prog<0) prog=0;
        soma+=prog;
    });

    let media=total?Math.round(soma/total):0;
    document.getElementById("total").innerText=total;
    document.getElementById("media").innerText=media+"%";
    document.getElementById("barra").style.width=media+"%";

    if(chart) chart.destroy();
    chart=new Chart(document.getElementById("grafico"),{
        type:"doughnut",
        data:{
            labels:["Concluído","Em Dia","Em Risco","Atrasado"],
            datasets:[{
                data:[0,0,0,0],
                backgroundColor:["#28a745","#007bff","#ffc107","#dc3545"]
            }]
        },
        options:{plugins:{legend:{position:"bottom"}}}
    });
}

async function salvar(){
    let linhas=document.querySelectorAll("#tabela tbody tr");
    let dados=[];
    linhas.forEach(l=>{
        dados.push({
            descricao:l.cells[0].innerText,
            progresso:l.cells[1].innerText,
            observacao:l.cells[2].innerText
        });
    });
    await fetch(URL_SCRIPT,{
        method:"POST",
        body:JSON.stringify(dados)
    });
    alert("Salvo no Google Drive ✅");
}

async function carregar(){
    let resposta=await fetch(URL_SCRIPT);
    let dados=await resposta.json();
    document.querySelector("#tabela tbody").innerHTML="";
    dados.forEach(d=>{
        adicionarLinha(d);
    });
    calcular();
}

document.addEventListener("input",calcular);
window.onload=carregar;

</script>

</body>
</html>
