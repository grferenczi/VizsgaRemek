function ConfirmSetDone(order_id,name){
    if(confirm('Biztosan kész a(z) '+order_id+' '+name+' folyamata?'))
    {
        //?fun=setdone&order_id='+order_id+'&proc='+name
        //fetch('index.php?fun=setdone&order_id='+order_id+'&proc='+name);
        MSG('/process',"PUT",{'order_id':order_id,'proc':name});
        location.reload();
    }
    
}
function SetLocation(id,location){
    MSG("/order","PUT",{'order_id':id,'location':location});
}
function OrderNew(){
    let date = document.getElementById("date").value;
    let time = document.getElementById("time").value;
    let saved = document.getElementById("saved").value;
    let title = document.getElementById("title").value;
    MSG("/order","POST",{'date':date,'time': time,'saved':saved,'title':title});
    location.href = "/order/nonready";
}
function OrderUpdate(order_id){
    let ddate = document.getElementById("deadline_date").value;
    let status = document.getElementById("status").value;
    let note = document.getElementById("note").value;
    let title = document.getElementById("title").value;
    MSG("/order","PUT",{'order_id':order_id,'deadline_date':ddate ,'status':status,'note':note,'title':title});
    location.reload();

}
function ProcessCreate(order_id){
    let name = document.getElementById("selected").value;
    let step = document.getElementById("step").value;
    let line = document.getElementById("line").value;
    let done = document.getElementById("done").checked;
    let req = [];
    if(document.getElementById("req0").value) req.push(document.getElementById("req0").value)
    if(document.getElementById("req1").value) req.push(document.getElementById("req1").value)
    if(document.getElementById("req2").value) req.push(document.getElementById("req2").value)
    if(document.getElementById("req3").value) req.push(document.getElementById("req3").value)
    req=req.join('|');
    if(name){
        MSG("/process","POST",{'order_id':order_id,'name':name ,'step':step,'line':line,'done':done,'req':req});
        location.reload();
    }
}
function ProcessDelete(order_id){
    let name = document.getElementById("selected").value;
    if(name){
        MSG("/process","DELETE",{'order_id':order_id,'name':name});
        location.reload();
    }
}
async function ChangeSelection(order_id,name){
    for(let i=0;i<4;i++){
        document.getElementById("req"+i).value=null;
    }
    document.getElementById("done").removeAttribute("checked")
    document.getElementById("step").value=null;
        document.getElementById("line").value=null;
    let resposne=  await fetch('/process?id='+order_id+'&proc='+name)
    .then(res=>res.json());
    if(resposne){
        document.getElementById("step").value=resposne.step;
        document.getElementById("line").value=resposne.line;
        if(resposne.done==1){document.getElementById("done").setAttribute("checked","")}
        let tmp = resposne.req!=null? resposne.req.split('|'):null;
        
        if(tmp){
            for(let i=0;i<tmp.length;i++){

            document.getElementById("req"+i).value=tmp[i];
            }
        }   
    }
}
function Login(){
   let id = document.getElementById("user_id").value;
   let uri = window.location.href;
   uri = uri.replace("/login","/");
    MSG("/login","POST",{'id':id});
    window.location = uri;
}

function Logout(){
    let uri = window.location.href;
    MSG("/login","DELETE");
    window.location.href = uri;
}

function MSG(url,sendmethod = "GET",payload=null){
   
    if((sendmethod=="GET"||sendmethod=="DELETE")){
        if(payload!=null){
            url+="?";
            for (let key in payload) {
                url+=key+"="+payload[key]+"&";
            }
            url=url.slice(0, -1);
        }
        return fetch(url, {method: sendmethod});}
    

    else if((sendmethod=="POST"||sendmethod=="PUT")){
        return fetch(url, {
            method: sendmethod,
            headers: {
            'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });
    }
}