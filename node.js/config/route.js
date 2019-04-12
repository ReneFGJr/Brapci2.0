const express = require("express");
const router = express.Router();

const source = require("../model/sources");
//const oai = require("../model/oai_pmh");
const oai = require("../model/oai_ListIdentifiers");

router.get("/", function(req, res) {
	//res.send(">>"+__dirname+"\\test.html");
	res.send('Welcome OAI Brapci');
	console.log('Welcome to Robot #01 - OAI Brapci')
});

router.route("/source").get(source.ListAll);

/************************************************** OAI HARVESTING ***************/
router.get("/oai", function(req, res) {	
	oai.readNext(req, res);
});

module.exports = router; 
