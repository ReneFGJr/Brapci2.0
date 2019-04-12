/*
 * @author Rene Faustino Gabriel Junior
 * @version 0.19.04.12
 * @title ListIdentifiers
 */

/* MODULES *****/
var db = require('../config/database');
var xml2js = require('xml2js');
const yyyymmdd = require('yyyy-mm-dd');
const fs = require("fs");
var request = require('request');

/* FUNCTIONS *****/
var oai = {
	/******************** FAZ A LEITURA DO PRÓXIMO REGISTRO *************/
	readNext : function(req, res) {
		var url = 'http://www.scielo.br/oai/scielo-oai.php?verb=ListIdentifiers&metadataPrefix=oai_dc_openaire&set=0103-3786&resumptionToken=HR__S0103-37862003000200008:0103-3786:::oai_dc';
		var html = { body : "none", url: url, error: "", length: 0, file : file};
		var file = '../node.js/oai/scielo-oai.php.xml';
		var h = oai.openSaveUrl(html);
		console.log(html);
		res.send(html);
	},
	openSaveUrl : function(obj) {
		var url = obj.url;
		var a = request(url, function(error, response, html) {
			console.log('File: ' + obj.file);
			//console.log('Read:' + html.length);
		});
		obj.length = a.html;
	}
}


module.exports = oai;
