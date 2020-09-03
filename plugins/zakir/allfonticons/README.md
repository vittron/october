
> This plugin provide a backend formwidget with ability to select font awesome icons starting from **4.0.0 version**

> Yes you can use any font-awesome icons starting from **4.0.0 version**

&nbsp;
## How to install
- Go to **Backend > Settings** and then go to install plugin page and type **Zakir.AllFontIcons** and install it.

## How to configure
- After installation, go to **Backend > Settings** and type **ALL FONT ICONS** and click on **Font-awesome CDN link**.
- Here you can add any font-awesome cdn link starting from **4.0.0 version**. by default **4.7.0 version** has been used.
- Use this url [Font-awesome cdn links](https://cdnjs.com/libraries/font-awesome), select your version, copy your css link and save it.
- Copy either **all.css** or **all.min.css** or **fontawesome.css** or **fontawesome.min.css** file.

## How to add form widget
- just add this code in any fields.yaml file and formwidget will start working with your font-awesome icons

		icon:
		    label: 'Select Icon'
		    type: zakir_allfonticons
		    placeholder: '-- Please Select --'
		
&nbsp;

## Optional
- When you install this plugin a component is registered with name **FontIconsCSS**.
- Add this into your **Layout/Page** and it will load your font-awesome icon css file.
- It is optional and you only need it if you want to load font-awesome icon css file on front-end.
