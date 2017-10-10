//JavaScript/jQuery stuff to make life easier
$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

$(function() {
    $('form').submit(function() {
        $('#result').text(JSON.stringify($('form').serializeObject()));
//        return false;
    });
});

function countInArray(array, what) {
    var count = 0;
    for (var i = 0; i < array.length; i++) {
        if (array[i] === what) {
            count++;
        }
    }
    return count;
}

function remove(arr, what) {
    var found = arr.indexOf(what);

    while (found !== -1) {
        arr.splice(found, 1);
        found = arr.indexOf(what);
    }
}



// Constants
var formjson='';

var Tabs = {
  ADD_FIELD_TAB: 0,
  FIELD_SETTINGS_TAB: 1,
  FORM_SETTINGS_TAB: 2
};


var FIELD_TYPES = [
  "text", "number", "textarea", "checkbox", "radio", "select", 
  "email", "subject", "name", "message", "tel", "date", "url"
];

// Root view model
var EditorViewModel = function(data) {
  var self = this;

  // Data
  this.form = new FormViewModel(data);
  this.currentTab = ko.observable(Tabs.ADD_FIELD_TAB);
  this.selectedField = ko.observable(null);

  // Helper data
  this.formSettingsSelected = ko.computed(function(){
    return this.currentTab() === Tabs.FORM_SETTINGS_TAB
  }, this);

  this.selectedFieldIndex = ko.computed(function() {
    return self.form.fields.indexOf(self.selectedField());
  }, this);

  this.formJSON = ko.computed(function(){
	formjson=ko.toJSON(self.form);
    return ko.toJSON(self.form);
  }, this);

  // Behaviour
  this.showFormSettings = function() {
    self.currentTab(Tabs.FORM_SETTINGS_TAB);
  };

  this.selectField = function(field) {
	self.selectedField(field);
    self.currentTab(Tabs.FIELD_SETTINGS_TAB);
  };

  this.selectFieldAtIndex = function(index) {
    if (index >= 0 && index < self.form.fields().length) {
      self.selectField(self.form.fields()[index]);
    }
  };

  // Duplicates the given field by delegating to FormViewModel
  this.duplicateField = function(field) {
    var field = self.form.duplicateField(field);
    if (field) {
      self.selectedField(field);
    }
  };

  // Adds a new field from given data. event is used to get the type of the
  // new field. Actually delegates to the FormViewModel
  this.addField = function(data, event) {
	counter();
    var type = $(event.target).data("type");
    data.type = type;
    var field = self.form.addField(data);

    // Scroll to the field
    if (field) {
      $("html, body").animate({
        scrollTop: $(".field-wrapper:last").offset().top
      }, 1000);
    }
  };

  // Removes the given field. Actually delegates to the FormViewModel
  this.removeField = function(field) {
	remove(id_name_array, field.id_name());
    if (field === self.selectedField()) {
      self.selectedField(null);
	}
    self.form.removeField(field);
    if (!self.form.hasFields() && self.currentTab() === Tabs.FIELD_SETTINGS_TAB) {
      self.currentTab(Tabs.ADD_FIELD_TAB);
    }
  };

  this.createFirstField = function() {
    var field = self.form.addField({type: 'textarea'});
    field.title("This is my first field, yeay!");
    self.selectField(field);
  };
  
  //Default form
  this.createDefault = function() {
  	counter()
    var name = self.form.addField({type: 'name'});
	counter()
    var email = self.form.addField({type: 'email'});
	counter()
	var subject= self.form.addField({type: 'subject'});
 	counter()
   	var message= self.form.addField({type: 'message'});	
    self.selectField(name);
  };  

  this.removeselectedField = function() {
    var selectedField = self.selectedField();
    if (selectedField !== null) {
      self.removeField(selectedField);
    }
  };

  this.selectNextField = function(){
    self.selectFieldAtIndex(self.selectedFieldIndex() + 1);
  };

  this.selectPrevField = function(){
    self.selectFieldAtIndex(self.selectedFieldIndex() - 1);
  };

  // Setup keyboard shortcuts
  $(document)
    .bind('keydown', 'backspace', this.removeselectedField)
    .bind('keydown', 'del', this.removeselectedField)
    .bind('keydown', 'j', this.selectNextField)
    .bind('keydown', 'k', this.selectPrevField);
};

var getDefaultDataForType = function(type) {
  switch (type) {
  case 'text': return {
	title: 'Text',
  };
  case 'number':
    return {
      title: "Number"
    };
  case 'textarea': return {
		title: 'Paragraph'
  };
  case 'checkbox':
    return {
      title: "Check all that apply",
      choices: [
        {choice: "First Choice"},
        {choice: "Second Choice"},
        {choice: "Third Choice"}
      ],
	  inline: false
    };
  case 'radio':
    return {
      title: "Select a Choice",
      choices: [
        {choice: "First Choice"},
        {choice: "Second Choice"},
        {choice: "Third Choice"}
      ]
    };
  case 'select':
    return {
      title: "Select a Choice",
      choices: [
        {choice: "First Choice"},
        {choice: "Second Choice"},
        {choice: "Third Choice"}
      ]
    };
  case 'section':
    return {
      title: "Section Break",
      instructions: "A description of the section goes here."
    };
  case 'page': return {};
  case 'shortname':
    return {
      title: "Name"
    };
  case "phone":
    return {
      title: "Phone"
    };
  case 'email':
    return {
      title: "Email",
	  is_required: true
    };
  case 'subject':
    return {
      title: "Subject",
    };
  case 'date':
    return {
      title: "Date",
	  placeholder: "MM/DD/YYYY"
    };	
  case 'url':
    return {
      title: "URL",
    };		
  case 'tel':
    return {
      title: "Telephone #",
	  placeholder: ""	  
    };			
  case 'name':
    return {
      title: "Name",
    };	
  case 'message':
    return {
      title: "Message",
    };		
  case "time":
  case "url":
  case "money":
  case "likert":
  default: return {}
  }
};

//Counter
var cc=0;
$(function() {
if (initial_form.counter){cc=initial_form.counter}
});

var counter=function(){
	cc=cc+1;
	return cc
}

var FormViewModel = function(data) {
  var self = this;
  var mapping = {
    'fields': {
      create: function(options) {
        return new FieldViewModel(options.data || []);
      }
    }
  };
  this.counter=initial_form.counter;
  this.name = ko.observable("Untitled");
  this.description = ko.observable("");
  this.fields = ko.observableArray([]);
  ko.mapping.fromJS(data, mapping, this);

  this.hasFields = ko.computed(function(){
    return this.fields().length !== 0;
  }, this);

  // Behaviour
  this.duplicateField = function(field) {
    var newFieldData = ko.toJS(field),
        index = ko.utils.arrayIndexOf(self.fields(), field),
        newField = new FieldViewModel(newFieldData);
    self.fields.splice(index, 0, newField);
    return newField;
  };

  this.addField = function(data) {
    if (data.type === undefined) {
      return false;
    }
	//console.log(self.fields());
    var newField = new FieldViewModel({type: data.type});
    ko.mapping.fromJS(getDefaultDataForType(data.type), {}, newField);
    self.fields.push(newField);
    return newField;
  };

  this.removeField = function(field) {
    self.fields.remove(field);
  };
};

// Helper
var traverse = function(o, func) {
  for (i in o) {
    func.apply(this, [o, i, o[i]]);
    if (typeof(o[i])=="object") {
      traverse(o[i],func);
    }
  }
};

FormViewModel.prototype.toJSON = function() {
	//hacky way to make sure that the counter updates properly
	if (ko.utils.unwrapObservable(this.counter)>cc){var cc_use=ko.utils.unwrapObservable(this.counter)}else{var cc_use=cc}
  
  var obj = {
    name: ko.utils.unwrapObservable(this.name),
	nickname: ko.utils.unwrapObservable(this.nickname),
	recipient_email: ko.utils.unwrapObservable(this.recipient_email),
    description: ko.utils.unwrapObservable(this.description),
	font_size: ko.utils.unwrapObservable(this.font_size),
	font_family: ko.utils.unwrapObservable(this.font_family),	
    hide_intro: ko.utils.unwrapObservable(this.hide_intro),
	button_color: ko.utils.unwrapObservable(this.button_color),
	redirect_url: ko.utils.unwrapObservable(this.redirect_url),	
	counter: cc_use,
    honey: ko.utils.unwrapObservable(this.honey),
    fields: ko.utils.unwrapObservable(this.fields)
  };

  // Remove ko mappings from the object recursively
  traverse(obj, function(object, key, value) {
    if (key === '__ko_mapping__') {
      delete object[key];
    };
  });

  return obj;
};

var	id_name_array=[];

var FieldViewModel = function(data) {
	var self = this;
	this.type = ko.observable();
	this.title = ko.observable("Untitled");
	this.is_required = ko.observable(false);
	this.inline = ko.observable(false);	
	this.append_array = ko.observableArray(['No', 'Text', 'Icon']);	
	this.prepend_array = ko.observableArray(['No', 'Text', 'Icon']);	
	this.append = ko.observable('No');
	this.prepend = ko.observable('No');
	this.prepend_text=ko.observable('');
	this.prepend_icon=ko.observable('');
	this.append_text=ko.observable('');	
	this.append_icon=ko.observable('');	
    this.show_prepend_text = ko.computed(function() {
		if (this.prepend() == 'Text'){return true}
		else {return false}
    }, this);
    this.show_prepend_icon = ko.computed(function() {
		if (this.prepend() == 'Icon'){return true}
		else {return false}
    }, this);	
    this.show_append_text = ko.computed(function() {
		if (this.append() == 'Text'){return true}
		else {return false}
    }, this);
    this.show_append_icon = ko.computed(function() {
		if (this.append() == 'Icon'){return true}
		else {return false}
    }, this);	
	this.inline = ko.observable(false);	
	this.placeholder = ko.observable("");
	this.instructions = ko.observable("");
	this.choices = ko.observableArray([]);
	this.id=cc;
	this.id_name_value = ko.observable(''); /*cjy stores the id_name */
	this.id_name = ko.computed({ 
		read: function () {
			if (!this.id_name_value()){ /* Creates name from type with no duplicates */
				var name=self.type();
				if (!name){return}
				var inc=1; var times=1;
				while (times > 0){
					times=countInArray(id_name_array, name);
					if (times>0){
						name=self.type()+inc.toString();
						inc++;
					}
				}
				id_name_array.push(name);			
				this.id_name_value(name);
			}
			return this.id_name_value()
		},
		write: function (value) { /* When someone enters the value you get this */
			var cleaned=value.replace(/ /g,'');
			this.id_name_value(cleaned);
		},
		owner: this
	});
  
  ko.mapping.fromJS(data, {}, this);

  // Data
  this.previewTemplateName = ko.computed(function(){
    return "tmp-field-preview-" + this.type();
  }, this);

  this.settingsTemplateName = ko.computed(function(){
    return "tmp-field-settings-" + this.type();
  }, this);

  this.hasChoices = ko.computed(function() {
    return this.choices && this.choices().length !== 0;
  }, this);
  
   this.placeholderNeeded = ko.computed(function() {
   var type=this.type();
	var allowed_types=['email', 'text', 'name', 'number', 'subject', 'message',  'textarea', 'tel', 'date', 'url']
	if (jQuery.inArray(type, allowed_types)!=-1){return true}
	else {return false}
   }, this); 
   
   this.inlineNeeded = ko.computed(function() {
   var type=this.type();
   var allowed_types=['checkbox', 'radio'];
	if (jQuery.inArray(type, allowed_types)!=-1){return true}
	else {return false}
   }, this);   

   this.addonsNeeded = ko.computed(function() {
   var type=this.type();
	var allowed_types=['email', 'text', 'name', 'number', 'subject', 'tel', 'date', 'url']
	if (jQuery.inArray(type, allowed_types)!=-1){return true}
	else {return false}
   }, this);      

  // Behaviour
  this.addChoice = function() {
    self.choices.push(ko.mapping.fromJS({"choice": ""}));
  };

  this.removeChoice = function(choice) {
    self.choices.remove(choice);
  };
};


FieldViewModel.prototype.toJSON = function() {
  return {
    type: ko.utils.unwrapObservable(this.type),
    title: ko.utils.unwrapObservable(this.title),
    is_required: ko.utils.unwrapObservable(this.is_required),
    inline: ko.utils.unwrapObservable(this.inline),	
    id_name: ko.utils.unwrapObservable(this.id_name),	
	append: ko.utils.unwrapObservable(this.append),
	prepend: ko.utils.unwrapObservable(this.prepend),
	prepend_text: ko.utils.unwrapObservable(this.prepend_text),
	prepend_icon: ko.utils.unwrapObservable(this.prepend_icon),
	append_text: ko.utils.unwrapObservable(this.append_text),
	append_icon: ko.utils.unwrapObservable(this.append_icon),	
	placeholder: ko.utils.unwrapObservable(this.placeholder),
    instructions: ko.utils.unwrapObservable(this.instructions),
    choices: ko.utils.unwrapObservable(this.choices),
	id: this.id
};
};

// Custom ko bindings
ko.bindingHandlers.tab = {
  init: function(element, valueAccessor) {
    var currentTab = valueAccessor();
    $(element).find('a').click(function() {
      currentTab($(this).parent().index());
    });
  },

  update: function(element, valueAccessor, allBindingsAccessor, viewModel) {
    var currentTab = valueAccessor()();
    $(element).find('li:nth(' + currentTab + ') a:first').trigger('click');
    // Dirty hack
    if (currentTab !== Tabs.FIELD_SETTINGS_TAB) {
      viewModel.selectedField && viewModel.selectedField(null);
    }
  }
};
