let selectorcount = 1;
if(Array.isArray(selectors))
{
    selectorcount = selectors.length;
}

for(let i = 0; i < selectorcount; i++)
{
    selectorname = selectors[i];
    if(!Array.isArray(selectors))
    {
        selectorname = selectors;
    }

    if(colorscheme = "dark")
    {
        tinymce.init({
            selector: '#' + selectorname,
            plugins : 'advlist autolink link image lists charmap print preview code',
            skin: "tinymce-5-dark",
            content_css: "tinymce-5-dark"
        });
    }
}