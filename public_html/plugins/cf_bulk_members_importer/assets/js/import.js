//selecting all required elements
const cfs_dropArea = document.querySelector(".cfs_drag-area"),
cfs_before_draging = document.querySelector(".cfs_before_draging"),
 cfs_dragged_filed = document.querySelector(".cfs_dragged_filed"),
 cfs_replace_button = document.querySelector(".cfs_replace_button"),
 cfs_filename = document.querySelector(".cfs_filename"),
cfs_dragText = cfs_dropArea.querySelector("header"),
cfs_button = cfs_dropArea.querySelector("button"),
cfs_input = cfs_dropArea.querySelector("input");
let cfs_file; //this is a global variable and we'll use it inside multiple functions
cfs_button.onclick = ()=>{
  cfs_input.click(); //if user click on the button then the input also clicked
}
cfs_replace_button.onclick = ()=>{
  cfs_input.click(); //if user click on the button then the input also clicked
}
cfs_input.addEventListener("change", function(){
  //getting user select file and [0] this means if user select multiple files then we'll select only the first one
  // console.log(this.files.length);
  if(this.files.length >=1 )
  {
    cfs_file = this.files[0];
  }else{
    cfs_file=cfs_file;
  }
  cfs_dropArea.classList.add("active");
  CFS_showFile(); //calling function
});
//If user Drag File Over DropArea
cfs_dropArea.addEventListener("dragover", (event)=>{
    event.preventDefault(); //preventing from default behaviour
    cfs_dropArea.classList.add("active");
});
//If user leave dragged File from DropArea
cfs_dropArea.addEventListener("dragleave", ()=>{
  cfs_dropArea.classList.remove("active");
});
//If user drop File on DropArea
cfs_dropArea.addEventListener("drop", (event)=>{
  event.preventDefault(); //preventing from default behaviour
  //getting user select file and [0] this means if user select multiple files then we'll select only the first one
  cfs_file = event.dataTransfer.files[0];
  CFS_showFile(); //calling function
});
function CFS_showFile(){
  let fileType = cfs_file.type; //getting selected file type
  var csvParsedArray = [];
  let validExtensions = ["text/csv"]; //adding some valid image extensions in array
  if(validExtensions.includes(fileType)){ //if user selected file is an image file
    let formss = document.getElementById("cfs_import_member");
    let formdata = new FormData(formss);
    formdata.append("files",cfs_file);
    let reader = new FileReader();
    let bytes = 50000;
    reader.onloadend = function (evt) {
      let lines = evt.target.result;
      if (lines && lines.length > 0) {
        let line_array = CFS_CSVToArray(lines);
        if (lines.length == bytes) {
          line_array = line_array.splice(0, line_array.length - 1);
        }
        var columnArray = [];
        var stringHeader = ``;
        for (let i = 0; i < line_array.length; i++) {
          let cellArr = line_array[i];
          for (var j = 0; j < cellArr.length; j++) {
            if(i == 0) {
              columnArray.push(cellArr[j].replace('ï»¿', ''));
              stringHeader += `<option value='${j}'> ${columnArray[j]} </option>`;
            }
          }
        }
        let headersd = document.getElementById("cfs_custom_header");
        headersd.style.display='block';
        let emailf = document.getElementById("cfs_custom_email");
        let namef = document.getElementById("cfs_custom_name");
        emailf.innerHTML=stringHeader;
        namef.innerHTML=stringHeader;
      }
    }
    let blob = cfs_file.slice(0, bytes);
    reader.readAsBinaryString(blob);

    cfs_filename.textContent=cfs_file.name;
    cfs_before_draging.style.display='none';
    cfs_dragged_filed.style.display='block';

  }else{
    alert("This is not a csv File!");
  }
}
function CFS_CSVToArray(strData, strDelimiter) {
  strDelimiter = (strDelimiter || ",");
  let objPattern = new RegExp(
    (
      "(\\" + strDelimiter + "|\\r?\\n|\\r|^)" +
      "(?:\"([^\"]*(?:\"\"[^\"]*)*)\"|" +
      "([^\"\\" + strDelimiter + "\\r\\n]*))"
      ),
    "gi"
    );
  let arrData = [[]];
  let arrMatches = null;
  while (arrMatches = objPattern.exec(strData)) {
    let strMatchedDelimiter = arrMatches[1];
    let strMatchedValue = [];
    if (strMatchedDelimiter.length && (strMatchedDelimiter != strDelimiter)) {
      arrData.push([]);
    }
    if (arrMatches[2]) {
      strMatchedValue = arrMatches[2].replace(new RegExp("\"\"", "g"),"\"");
    } else {
      strMatchedValue = arrMatches[3];
    }
    arrData[arrData.length - 1].push(strMatchedValue);
  }
  return (arrData);
}

document.getElementById("cfs_import_member").onsubmit=function(event)
{
  event.preventDefault();
  let url = document.getElementById("cfaddstudent_ajax").value;
  let btn = document.getElementById("cfs_importer_button");
  
  if(cfs_file != undefined ) {
      btn.innerHTML=`Importing.. <span class=" spinner-border spinner-border-sm"></span></button>`;
      var form_data = new FormData(this);     
      form_data.append('filess', cfs_file);
      
      var xhttp = new XMLHttpRequest();
      console.log(form_data);
      
      xhttp.open("POST", url, true);
      xhttp.onload = function(event) {
          if (xhttp.status == 200) {
            let data=this.responseText;
            console.log(this.responseText);
            btn.innerHTML="Import";
            let res = JSON.parse(data);
            if(res.status==1)
            {
              window.location=location.href;
            }else{
              alert(res.message);
            }
          } else {
            window.location=location.href;
          }
      }
      let progres = document.querySelector('.cfs_progress');
      xhttp.upload.addEventListener("progress", function (evt) {
        if (evt.lengthComputable) {
            var percentComplete = evt.loaded / evt.total;
            progres.style.width=percentComplete * 100 + '%';
            if (percentComplete === 1) {
              progres.classList.add("cfs_hide");
            }
        }
    }, false);
    xhttp.send(form_data);
  }
}
