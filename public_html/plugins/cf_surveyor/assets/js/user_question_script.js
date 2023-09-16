var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Display the current tab
function showTab(n) {
  // This function will display the specified tab of the form ...
  var x = document.getElementsByClassName("tab");
  x[n].style.display = "block";
    // ... and fix the Previous/Next buttons:
  if (n == 0) {
    //document.getElementById("prevBtn").style.display = "none";
  } else {
    //document.getElementById("prevBtn").style.display = "inline";
  }
  if (n == (x.length - 1)) {
    //document.getElementById("nextBtn").innerHTML = "Submit";
  } else {
    // document.getElementById("nextBtn").innerHTML = "Next";
  }
  mybr=(n+1)*100/x.length;
  mybr=mybr+"%";
  document.getElementById("progbr").style.width=mybr;
}

function nextPrev(n) {
  // This function will figure out which tab to display
  var x = document.getElementsByClassName("tab");
  // Exit the function if any field in the current tab is invalid:
  if (n == 1 && !validateForm()) return false;
  // Hide the current tab:
  

  x[currentTab].style.display = "none";
  // Increase or decrease the current tab by 1:
  currentTab = currentTab + n;
  // if you have reached the end of the form... :
  if (currentTab >= x.length) {
    //...the form gets submitted:
    document.getElementById("regForm").submit();
    return false;
  }
  // Otherwise, display the correct tab:
  showTab(currentTab);
}

function validateForm() {
  // This function deals with validation of the form fields
  var x, y, i, valid = false, flag;
  x = document.getElementsByClassName("tab");
  y = x[currentTab].getElementsByTagName("input");

  
  // A loop that checks every input field in the current tab:
  for (i = 0; i < y.length; i++) {

    console.log(y[i].checked)
    if (y[i].checked == true) {
    flag=y[i].checked;
    break;
    }
    else
    {
      flag=false;
    }
  }
  if(flag==true)
  {
  valid=true;
  }
  else
  {
    alert('Please Select an option');
    valid=false
  }  
  return valid; // return the valid status
}
function fun(radiodiv)
{
radiodiv.checked=true;
}

/*These below scripts functions are to support file add_questions.php admin side file.*/
function delete_option(i)
{
delete_element=document.getElementById("cfopt"+i);
delete_element.remove();
}

function add_options() {
var i = parseInt(document.getElementById('number').value, 10);
i = isNaN(i) ? 0 : i;
i++;
document.getElementById('number').value = i;
var cfoptions=document.getElementById("options");
var cfdiv = document.createElement("DIV");
cfdiv.setAttribute("id","cfopt"+i);
cfdiv.setAttribute("class","form-group py-0 mx-1");
var cflabel=document.createElement("LABEL");
cflabel.innerHTML="Option "+i;
cfdiv.appendChild(cflabel);
var cross=document.createElement("I");
cross.setAttribute("class","fas fa-times-circle delinp text-right ml-5");
cross.setAttribute("counter",i);
cross.setAttribute('onclick','delete_option('+i+');');
cfdiv.appendChild(cross);
cfdiv.appendChild(document.createElement("BR"));
var cfinp = document.createElement("INPUT");
cfinp.setAttribute("type", "text"); 
cfinp.setAttribute("counter",i);
cfinp.setAttribute("required","");
cfinp.setAttribute("id","opt"+i);
cfinp.setAttribute("name","opt"+i);
cfinp.setAttribute("class","form-control");
cfinp.setAttribute("placeholder", "Enter Option "+i);
cfdiv.appendChild(cfinp);
cfoptions.appendChild(cfdiv);
}