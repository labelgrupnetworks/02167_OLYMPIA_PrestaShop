/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Musaffar Patel
 * @copyright 2016-2021 Musaffar Patel
 * @license   LICENSE.txt
 */

MPProductSearchWidget = function(id, selected_products = '') {
	var self = this;
	self.id = id;
	self.wrapper = '#'+id;
	self.multiselect = true;
	self.selected_products = selected_products;

	self.pk = 'id_product'; //unique identifier for each row,. data binded to database table

	self.showResultsList = function () {
		$(self.wrapper).find("#product-search-results").slideDown();
	};

	self.hideResultsList = function () {
		$(self.wrapper).find("#product-search-results").slideUp();
	};

	self.clearResultsList = function () {
		$(self.wrapper).find("#search-results-table tbody tr.result-item").remove();
	};

	self.addToResultList = function (jsonRow) {
		$results_table = $(self.wrapper).find("#search-results-table");

		var $cloned = $results_table.find("tr.cloneable").clone();
		$cloned.removeClass("cloneable");
		$cloned.removeClass("hidden");
		$cloned.addClass("result-item");

		$.each(jsonRow, function (key, value) {
			$cloned.find("td."+key).html(value);
			$cloned.attr("data-"+key, value);
		});
		$cloned.appendTo($results_table.find("tbody"));
	};

	/**
	 * For multi select, gets all product ID selected as a coma separated string
 	 */
	self.compileProductIDString = function() {
		let string = '';
		$(self.wrapper).find("a.product-entry").each(function () {
			string = string + $(this).attr('data-id-product') + ',';
		});
		string = string.replace(/,\s*$/, ""); //remove last comma
		$(self.wrapper).find("input[name='id_product']").val(string);
	};

	/**
	 * Add new product Item to selected products control
 	 * @param id_product
	 * @param name
	 */
	self.addProductItem = function(id_product, name) {
		$multiselect_wrapper = $(self.wrapper).find(".mp-multiselect");
		$multiselect_wrapper.append("<a class='product-entry' data-id-product='"+ id_product+"'><span>" + name + "</span><i class='icon icon-close'></i></a>");
	};


	/**
	 * On product result click
 	 * @param id
	 * @param selected_text
	 */
	self.onResultSelect = function (id, selected_text) {
		if (self.multiselect) {
			self.addProductItem(id, selected_text);
			self.compileProductIDString();
		} else {
			$(self.wrapper).find('input[name="' + self.pk + '"]').val(id);
			$(self.wrapper + " input#product_search").val(selected_text);
		}
	};

	self.popupProcessSearch = function (search_string, scope) {
		var url = module_ajax_url_lrp + '?section=mpproductsearchwidgetcontroller&action=processsearch';
		$.ajax({
			type: 'POST',
			url: url,
			async: true,
			cache: false,
			dataType: "json",
			data: {
				'id' : self.id,
				'search_string': search_string,
				'scope': scope
			},
			success: function (jsonData) {
				self.clearResultsList();
				self.showResultsList();
				for (var x = 0; x <= jsonData.length - 1; x++) {
					self.addToResultList(jsonData[x])
				}
				return false;
			}
		});
	};

	/**
	 * Add all items from the selected products array to the selected products control
 	 * @returns {boolean}
	 */
	self.renderSelectedProducts = function() {
		if (self.selected_products == '' || selected_products == null) {
			return false;
		}

		for (i=0; i<=self.selected_products.length-1;i++) {
			self.addProductItem(self.selected_products[i].id, self.selected_products[i].name);
		}
		self.compileProductIDString();
	};

	/**
	 * Allow multiple instances to be instantiated without multiple events being triggered
 	 */
	self.unbind = function() {
		$("body").off("keyup", self.wrapper + " input[name='product_search']");
		$("body").off("click", self.wrapper + " tr.result-item");
		$("body").off("click", self.wrapper + " a.product-entry i.icon-close");
	};

	self.init = function () {
		self.unbind();
		self.renderSelectedProducts();
	};
	self.init();

	/**
	 * Search field kep up event
 	 */

	$("body").on("keyup", self.wrapper + " input[name='product_search']", function () {
		if ($(this).val() == '' || $(this).val().length < 3) {
			self.clearResultsList();
			self.hideResultsList();
			return false;
		}
		self.popupProcessSearch($(this).val(), $('#' + self.popupFormId + " #scope").val())
	});

	$("body").on("click", self.wrapper + " tr.result-item", function () {
		self.onResultSelect($(this).attr("data-"+self.pk), $(this).attr("data-name"));
		self.hideResultsList();
	});

	/**
	 * On product entry (multi select) delete click
 	 */
	$("body").on("click", self.wrapper + " a.product-entry i.icon-close", function () {
		$(this).parents("a.product-entry").remove();
		self.compileProductIDString();
	});

};

