document.addEvent("domready", function() 
{

	var container = $('output_panel');
	var sidebar = $('sidebar');


	var tabPane = new TabPane(container, 
	{
		tabSelector: "li",
		contentSelector: "div.content",
		activeClass: "active",
	}, 
	function() 
	{
		var showTab = window.location.hash.match(/tab=(\d+)/);
		return showTab ? showTab[1] : 0;
	});

	container.addEvent("click:relay(.remove)", function(e) 
	{
		// stop the event from bubbling up and causing a native click
		e.stop();
		var parent = this.getParent(".tab");
		// find the index for the tab that needs to be closed 
		var index = container.getElements(".tab").indexOf(parent);
		// close the tab (closeTab takes care of selecting an adjacent tab) 
		tabPane.closeTab(index);
	});
	
	container.addEvent('click:relay(.hide)', function() {
		console.log("relay sidebar .tab")
	    var tab = this.getParent('.tab');
	    var index = container.getElements('.tab').indexOf(tab);
	    var content = container.getElements('div.content')[index];
	    content.setStyle('display', 'none');
	    
	    sidebar.adopt(tab, content);
	    
	    tabPane.showTab(Math.max(index, container.getElements('.tab').length - 1));
	});

	sidebar.addEvent('click:relay(.tab)', function() {
	    var tab = this;
	    var index = sidebar.getElements('.tab').indexOf(tab);
	    var content = sidebar.getElements('div.content')[index];
	    var numTabs = container.getElements('.tab').length;
	    
	    if(numTabs > 1)
	    {
	    	tab.inject(container.getElements('.tab')[numTabs - 1], 'after');
	    	content.inject(container.getElements('div.content')[numTabs - 1], 'after');
	    }
	    else{
	    	tab.inject(container)
	    	content.inject(container);
	    }
	});
	
	
	/*var fragment = document.URL.split("#")[1]
	if(fragment && fragment.indexOf("tab") != -1)
	{
		var index = $("output_panel").getElements(".tab").indexOf($(fragment))
		tabPane.showTab(index, $(fragment))
	}*/
	
	/* $("new-tab").addEvent("click", function() {
		var title = $("new-tab-title").get("value");
		var content = $("new-tab-content").get("value");

		if (!title || !content) {
			window.alert("Title or content text empty, please fill in some text.");
			return;
		}

		$("output_panel").getElement("ul").adopt(new Element("li", {"class": "tab", text: title}).adopt(new Element("span", {"class": "remove", html: "&times"})));
		$("output_panel").adopt(new Element("p", {"class": "content", text: content}).setStyle("display", "none"));
	}); */
});