//language manager
//debugger;
var cf_tinymce_lang="en_US";
try
{
    let langs={
        "lang_english_en": "en_us",
        "lang_dutch_nl": "nl",
        "lang_french_fr": "fr_FR",
        "lang_german_de": "de",
        "lang_greek_el": "el",
        "lang_italian_itl": "it",
        "lang_japanese_ja": "ja",
        "lang_hindi_hi": "hi_IN",
        "lang_arabic_ar": "ar",
        "lang_danish_da": "da",
        "lang_korean_ko": "ko_KR",
        "lang_romanian_ro": "ro",
        "lang_spanish_sp": "es",
        "lang_polish_pl": "pl",
        "lang_portuguese_po": "pt_PT",
        "lang_malay_ml": "en_us",
        "lang_norwegian_no": "nb_NO"
    };
    if(cf_registered_languages !==undefined && langs[cf_current_selected_language] !==undefined)
    {
        cf_tinymce_lang=langs[cf_current_selected_language];
    }
}catch(err){console.log(err);}

function t(txt,arr=[])
{
    try
    {
        let has_nbsp=false;
        if(cf_registered_languages !==undefined)
        {
            let _txt=txt;
            _txt=_txt.toString().trim();

            if(_txt.indexOf("&nbsp;")>-1)
            {
                has_nbsp=true;
            }
            _txt=_txt.replace(/&nbsp;/g," ");


            try
            {
                let html_entities=new HTMLEntities();
                _txt=html_entities.encode(_txt);

                _txt=_txt.replace(/&amp;/gi,'&');
                _txt=_txt.replace(/&apos;/gi,'\'');
                _txt=_txt.replace(/&rsquo;/gi,'\'');
                _txt=_txt.replace(/&nbsp;/gi,' ');
            }catch(err){console.log(err);}
            //alert(_txt);

            let num_reg=/^[0-9]+([\.,0-9]*[0-9]+)*$/g;

            let t_found=false;
            if(num_reg.test(_txt))
            {
                _txt=_txt.split("");
                for(let i=0;i<_txt.length;i++)
                {
                    if(_txt[i]=="." || _txt[i]==",")
                    {continue;}
                    let trans=cf_registered_languages[_txt[i]].trim();
                    _txt[i]=(trans.length>0)? trans:_txt[i];
                }
                _txt=_txt.join("");
                t_found=true;
            }
            else
            {
                if(cf_registered_languages[_txt] !==undefined)
                {   
                    let trans=cf_registered_languages[_txt].trim();
                    _txt=(trans.length>0)? trans:_txt;
                    t_found=true;
                }
            }
            //alert(_txt);
            
            for(let i=0;i<arr.length;i++)
            {
                let j=i+1;
                let reg=new RegExp(`\\\$\\\{${j}\\\}`,'g');
                //console.log(reg);
                _txt=_txt.replace(reg,arr[i]);
            }
            
            if(has_nbsp)
            {
                _txt=_txt.replace(/\s/g,"&nbsp;")
            }
            if(t_found)
            {txt=_txt;}
        }
    }catch(err)
    {
        console.log(err);
    }
    return txt;
}

function w(txt,arr)
{
    document.write(t(txt,arr));
}