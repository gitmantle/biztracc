spellWin = {
    resumeAfterEditing: function () {
        this.pickup();
        spellWin.regroup()
    },
    isStillOpen: function () {
        return window.document != null;
    },
    providerID: null,
    provider: function () {
        return livespell.spellingProviders[this.providerID];
    }


	,
    lang: {
        localize_ui: function () {
            document.title = this.fetch("WIN_TITLE");
            $$("btnAddToDict").value = this.fetch("BTN_ADD_TO_DICT");
            $$("btnAutoCorrect").value = this.fetch("BTN_AUTO_CORECT");
            $$("btnOptionsCancel").value = $$("btnLangCancel").value = this.fetch("BTN_CANCEL");
            $$("btnChange").value = this.fetch("BTN_CHANGE");
            $$("btnChangeAll").value = this.fetch("BTN_CHANGE_ALL");
            $$("btnCancel").value = this.fetch("BTN_CLOSE");
            $$("btnIgnoreAll").value = this.fetch("BTN_IGNORE_ALL");
            $$("btnIgnore").value = this.fetch("BTN_IGNORE_ONCE");
            $$("btnUndoManualEdit").value = this.fetch("BTN_CLEAR_EDIT");
            $$("btnUndo").value = this.fetch("BTN_UNDO");
            $$("btnAllDone").value = $$("btnOptionsOK").value = $$("btnLangOK").value = this.fetch("BTN_OK");
            $$("btnShowOptions").value = this.fetch("BTN_OPTIONS");
            $$("btnResetDict").value = $$("btnResetAutoCorrect").value = this.fetch("BTN_RESET");
            $$("tSum").innerHTML = this.fetch("DONESCREEN_MESSAGE");
            $$("fldLanguageLab").innerHTML = this.fetch("LABEL_LANGAUGE");
            $$("SuggestionsLab").innerHTML = this.fetch("LABEL_SUGGESTIONS");
            $$("fldLanguageMultipleLabText").innerHTML = this.fetch("LANGUAGE_MULTIPLE_INSTRUCTIONS");
            $$("lMeaningLink").innerHTML = this.fetch("LOOKUP_MEANING");
            $$("optCaseSensitiveText").innerHTML = this.fetch("OPT_CASE_SENSITIVE");
            $$("optAllCapsbText").innerHTML = this.fetch("OPT_IGNORE_CAPS");
            $$("optNumericText").innerHTML = this.fetch("OPT_IGNORE_NUMERIC");
            $$("optSentenceText").innerHTML = this.fetch("OPT_SENTENCE_AWARE");
            $$("btnResetDictLabText").innerHTML = this.fetch("OPT_PERSONAL_AUTO_CURRECT");
            $$("btnResetAutoCorrectLabText").innerHTML = this.fetch("OPT_PERSONAL_DICT");
        },

        fetch: function (index) {
            return livespell.lang.fetch(spellWin.providerID, index);
        }
    },
    allDone: false,
    docs: [],
    wordstocheck: [],
    currentDoc: 0,
    currentToken: 0,
    currentReason: "",
    editcount: 0,
    suggestionsInMotion: true,

    undo: {
        bookmarks: [],
        set: function (action) {
            var mem = {};
            var targets = "currentDoc,currentReason,currentToken,editcount".split(",");
            for (var i = 0; i < targets.length; i++) {
                mem[targets[i]] = spellWin[targets[i]]
            }
            mem.word = spellWin.tokens.getCurrent();
            mem.docs = this.arrayCopy(spellWin.docs)
            mem.action = action + "";
            if (action === "ADD") {
                mem.add = livespell.cookie.get("SPELL_DICT_USER");
            }
            if (action === "AUTO") {
                mem.auto = livespell.cookie.get("SPELL_AUTOCORRECT_USER");
            }
            this.bookmarks.push(mem);
            if (this.bookmarks.length > spellWin.provider().UndoLimit) {
                this.bookmarks.shift();
            }
        },
        get: function () {


            var mem = this.bookmarks.pop();
            if (!mem) {
                return
            };
            var targets = "currentDoc,currentReason,currentToken,editcount".split(",");
            for (var i = 0; i < targets.length; i++) {
                spellWin[targets[i]] = mem[targets[i]]
            }
            delete spellWin.docs;
            spellWin.docs = this.arrayCopy(mem.docs)
            delete livespell.cache.ignore[mem.word]
            if (mem.action === "ADD") {
                spellWin.actions.deletePersonal();
                livespell.cookie.set("SPELL_DICT_USER", mem.add);
            }
            if (mem.action === "AUTO") {
                spellWin.actions.deleteAutoCorrect();
                livespell.cookie.set("SPELL_AUTOCORRECT_USER", mem.auto);
            }
            spellWin.tokenize();
            spellWin.moveNext();
        },
        arrayCopy: function (ain) {
            var aout = [];
            for (var it in ain) {
                aout[it] = ain[it]
            }
            return aout;
        }
    },
    tokens: {
        value: [],
        countWords: function () {
            var num = 0;
            for (var i = 0; i < this.value.length; i++) {
                for (var j = 0; j < this.value[i].length; j++) {
                    if (this.isWord(i, j)) {
                        num++
                    }
                }
            }
            return num;
        },
        next: function () {
            spellWin.currentToken++
            if (spellWin.currentToken > this.value[spellWin.currentDoc].length) {
                spellWin.currentDoc++;
                spellWin.currentToken = 0;
            }
        },
        set: function (i, j, val) {
            this.value[i][j] = val;
        },
        setCurrent: function (val) {
            this.value[spellWin.currentDoc][spellWin.currentToken] = val;
        },
        getCurrent: function () {
            return this.value[spellWin.currentDoc][spellWin.currentToken];
        },
        isWord: function (i, j) {
            return livespell.test.isword(this.value[i][j])
        },
        startsSentence: function (i, j) {
            return (j < 2 || livespell.test.eos(this.value[i][j - 1]))
        },
        findAndReplace: function ($$from, $$to, $$toCase) {
            for (var i = 0; i < this.value.length; i++) {
                for (var j = 0; j < this.value[i].length; j++) {
                    if (this.isWord(i, j) && this.value[i][j].toLowerCase() == $$from.toLowerCase()) {
                        this.value[i][j] = livespell.str.toCase($$to, livespell.str.getCase(this.value[i][j]), this.startsSentence(i, j));
                        spellWin.editcount++;
                    }
                }
            }
        },
        appetureXHTML: function (i, j, green) {
            var $$appeture = this.appeture(i, j);
            $$out = "";
            for (var k = $$appeture.min; k <= $$appeture.max; k++) {
                $$fragment = livespell.str.stripTags(this.value[i][k]);
                if (k == j) {
                    if (!green) {
                        $$fragment = '<span id="highlight">' + $$fragment + "</span>";
                    } else {
                        $$fragment = '<span id="highlightGrammar">' + $$fragment + "</span>";
                    }
                }
                $$out += $$fragment;
            }

            return $$out;
        },
        setAppeture: function (i, j, value) {
            var $$appeture = this.appeture(i, j);
            for (var k = $$appeture.min; k <= $$appeture.max; k++) {
                if (k == $$appeture.min) {
                    this.value[i][k] = value;
                } else {
                    this.value[i][k] = "";
                }
            }
            spellWin.currentToken = $$appeture.min;

        },
        appetureText: function (i, j) {
            var $$appeture = this.appeture(i, j);
            $$out = "";
            for (var k = $$appeture.min; k <= $$appeture.max; k++) {
                $$fragment = this.value[i][k];
				if($$fragment){
                $$out += $$fragment;
			}
            }
           
            return $$out;
        },
        appeture: function (i, j) {
            var doclen = this.value[i].length;
            var found = false
            for (var $$min = j; $$min > 0 && $$min > j - 20 && !found; $$min--) {
                if (livespell.test.eos(this.value[i][$$min])) {
                    $$min++;
                    found = true;
                    break;
                }
            }
            found = false
            for (var $$max = j; $$max < doclen && $$max < j + 20 && !found; $$max++) {
                if (livespell.test.eos(this.value[i][$$max])) {
                    $$max--;
                    found = true;
                    break;
                }
            }
            var $$appeture = {};
            $$appeture.min = $$min;
            $$appeture.max = $$max;
            return $$appeture;
        }
    },
    actions: {


        registerclose: function () {
            if (spellWin.allDone) {
                spellWin.provider().onDialogCompleteNET();
                spellWin.provider().onDialogComplete();
                spellWin.provider().__SubmitForm();
            } else {
                spellWin.provider().onDialogClose();
            }

        },
        done: function () {
            spellWin.provider().onDialogCompleteNET();
            spellWin.provider().onDialogComplete();
            spellWin.provider().__SubmitForm();
            spellWin.allDone = false;
            if (spellWin.provider().CustomOpenerClose)
            { spellWin.provider().CustomOpenerClose() }
            else if (window.opener) {
                window.close();
            } else if (dialogArguments && dialogArguments.document) {
                window.close();
            }
        },
        cancel: function () {

            spellWin.provider().onDialogCancel();
            if (spellWin.provider().CustomOpenerClose)

            { spellWin.provider().CustomOpenerClose() }
            else if (window.opener) {
                window.close();
            } else if (dialogArguments && dialogArguments.document) {
                window.close();
            }
        },
        changeLanguage: function () {
            if (spellWin.ui.getMenuValue("fldLanguage") == spellWin.lang.fetch("LANGUAGE_MULTIPLE")) {
                spellWin.optionsMenu.showMultiLang(true)

            } else {
                spellWin.provider().Language = spellWin.ui.getMenuValue("fldLanguage");
                spellWin.provider().onChangeLanguage(spellWin.ui.getMenuValue("fldLanguage"))
                spellWin.regroup();
            }
        },
        changeMultiLanguage: function () {
            spellWin.provider().Language = spellWin.ui.getMenuValue("fldLanguageMultiple");
            spellWin.provider().onChangeLanguage(spellWin.ui.getMenuValue("fldLanguageMultiple"))
            spellWin.optionsMenu.showMultiLang(false)
            spellWin.ui.setupLanguageMenu();
            spellWin.regroup();
        },
        deletePersonal: function () {
            livespell.cookie.erase("SPELL_DICT_USER");
        },
        deleteAutoCorrect: function () {
            livespell.cookie.erase("SPELL_AUTOCORRECT_USER");
        },
        ignoreOnce: function () {
            spellWin.undo.set();
            spellWin.provider().onIgnore(spellWin.tokens.getCurrent());
            spellWin.tokens.next();
            return spellWin.moveNext();
        },
        ignoreAll: function () {
            spellWin.undo.set();
            livespell.cache.ignore[spellWin.tokens.getCurrent().toLowerCase()] = true;
            spellWin.provider().onIgnoreAll(spellWin.tokens.getCurrent());
            spellWin.tokens.next();
            return spellWin.moveNext();
        },
        changeCurrent: function () {
            spellWin.undo.set();
            spellWin.editcount++
            if (spellWin.ui.editingNow) {


                spellWin.tokens.setAppeture(spellWin.currentDoc, spellWin.currentToken, $$("fldTextInput").value);


                spellWin.provider().onChangeWord(spellWin.tokens.getCurrent(), $$("fldTextInput").value);
                spellWin.ui.showEdit(false);
                spellWin.moveNext();
                return;
            }
            var oS = $$("fldSuggestions");
            var os_value = spellWin.ui.getMenuValue("fldSuggestions")
            if (oS.disabled) {
                if (os_value == "__*DEL*__") {
                    spellWin.provider().onChangeWord(spellWin.tokens.getCurrent(), "");
                    spellWin.tokens.setCurrent("");
                    spellWin.tokens.set(spellWin.currentDoc, spellWin.currentToken - 1, livespell.str.rtrim(spellWin.tokens.value[spellWin.currentDoc][spellWin.currentToken - 1]))
                }
                if (os_value == "NONE") {
                    spellWin.tokens.next();
                    return spellWin.moveNext();
                }


            } else {
                if (os_value == "__*REG*__") {
                    var a

                    if (spellWin.provider().isUniPacked) {
                        a = "javascriptspellcheck";

                    }
                    else if (spellWin.provider().ServerModel.toLowerCase() == "asp.net") {
                        a = "aspnetspell";

                    } else {
                        a = "phpspellcheck";
                    }
                    window.open("h" + "tt" + "p" + ":" + "/" + "/w" + "ww." + a + ".c" + "om/Pur" + "" + "cha" + "se")

                } else {
                    spellWin.provider().onChangeWord(spellWin.tokens.getCurrent(), os_value);
                    spellWin.tokens.setCurrent(os_value);
                }
            }

            spellWin.tokens.next();
            //Notify();
            return spellWin.moveNext();
        },
        changeAll: function () {
            spellWin.undo.set();
            var $$from = spellWin.tokens.getCurrent()
            var $$to = spellWin.ui.getMenuValue("fldSuggestions");
            var $$toCase = livespell.str.getCase($$to);
            spellWin.tokens.findAndReplace($$from, $$to, $$toCase);
            spellWin.provider().onChangeAll($$from, $$to);
            spellWin.tokens.next()
            spellWin.moveNext();
        },
        addPersonal: function (word) {
            spellWin.undo.set("ADD");
            word = spellWin.tokens.getCurrent()
            livespell.userDict.add(word)
            spellWin.provider().onLearnWord(spellWin.tokens.getCurrent());
            spellWin.moveNext();
        },
        addAutoCorrect: function () {
            spellWin.undo.set("AUTO");
            $$current_cookie = livespell.cookie.get("SPELL_AUTOCORRECT_USER");
            var $$from = spellWin.tokens.getCurrent()
            var $$to = spellWin.ui.getMenuValue("fldSuggestions");
            var $$toCase = livespell.str.getCase($$to);
            spellWin.tokens.findAndReplace($$from, $$to, $$toCase);
            if ($$current_cookie) {
                $$current_cookie = livespell.str.chr(1) + $$current_cookie
            }
            $$current_cookie = $$from + "->" + $$to + "#" + $$toCase + $$current_cookie;
            livespell.cookie.setLocal("SPELL_AUTOCORRECT_USER", $$current_cookie);
            spellWin.provider().onLearnAutoCorrect($$from, $$to);
            spellWin.moveNext();
        }
    },
    optionsMenu: {
        showMultiLang: function (flag) {
            spellWin.ui.show("multiLangForm", flag);
            spellWin.ui.show("MainForm", !flag);
            var fLang = $$("fldLanguage")
            if (!flag) {
                for (var i = 0; i < fLang.options.length; i++) {
                    if (fLang.options[i].value == spellWin.provider().Language) {
                        fLang.selectedIndex = i;
                    }
                }
            }
        },
        show: function (flag) {
            if (flag) {
                var pdict = livespell.cookie.get("SPELL_DICT_USER")
                var intPersonalEntries = pdict.length ? pdict.split(livespell.str.chr(1)).length : 0;
                $$("tDictCount").innerHTML = intPersonalEntries + " " + spellWin.lang.fetch("OPT_ENTRIES");
                spellWin.ui.enable("btnResetDict", intPersonalEntries > 0);
                var pauto = livespell.cookie.get("SPELL_AUTOCORRECT_USER")
                var intPersonalAutoEntries = pauto.length ? pauto.split(livespell.str.chr(1)).length : 0;
                $$("tAutoCorrectCount").innerHTML = intPersonalAutoEntries + " " + spellWin.lang.fetch("OPT_ENTRIES");
                spellWin.ui.enable("btnResetAutoCorrect", intPersonalAutoEntries > 0);
                $$("optCaseSensitive").checked = spellWin.provider().CaseSensitive;
                $$("optAllCaps").checked = spellWin.provider().IgnoreAllCaps;
                $$("optNumeric").checked = spellWin.provider().IgnoreNumeric;
            }
            spellWin.ui.show("optForm", flag);
            spellWin.ui.show("MainForm", !flag);
        },
        set: function () {
            spellWin.provider().CaseSensitive = $$("optCaseSensitive").checked;
            spellWin.provider().IgnoreAllCaps = $$("optAllCaps").checked;
            spellWin.provider().IgnoreNumeric = $$("optNumeric").checked;
            spellWin.provider().CheckGrammar = $$("optSentence").checked;
            this.show(false);
            spellWin.moveNext();
        }
    },
    init: function () {
        this.handshake();
        this.pickup();
        this.setTheme();
        this.hideButtons();
        spellWin.provider().setFieldListeners();
        setInterval(this.openerAware, 100);
        //

        this.lang.localize_ui();
        $$("optSentence").checked = spellWin.provider().CheckGrammar;
        this.ui.setLoadingMessage();

        spellWin.tokenize();
        this.autoCorrectOnLoad();
        livespell.userDict.load();
        this.ui.disableAll();
        spellWin.buildWordQue();
        this.suggestionsInMotion = true;

        this.sendInitialAJAXRequest();
    },

    openerAware: function () {

        if (window.opener) { return; }

        if (dialogArguments && dialogArguments.document) { return; }
        window.close();
    }
	,
    regroup: function () {
        this.ui.setLoadingMessage();
        spellWin.tokenize();
        this.ui.disableAll();
        spellWin.buildWordQue();

        this.sendInitialAJAXRequest()
        this.suggestionsInMotion = true;
    },
    nextSuggestionChunk: function () {
        var chunksize = 8;
        livespell.cache.suglist = [];
        for (var i = spellWin.currentDoc; i < this.tokens.value.length; i++) {
            for (var j = (i == spellWin.currentDoc ? spellWin.currentToken : 0); j < this.tokens.value[i].length; j++) {
                var spelltest = livespell.test.spelling(this.tokens.value[i][j], spellWin.provider().Language);
                var unknown = (!livespell.cache.suggestions[spellWin.provider().Language][this.tokens.value[i][j]]);
                if (this.tokens.isWord(i, j) && !spelltest && unknown) {
                    livespell.cache.suglist.safepush(this.tokens.value[i][j])
                    if (livespell.cache.suglist.length >= chunksize) {
                        return this.fetchSuggestionRequest()
                    }
                }
            }
        }
        if (livespell.cache.suglist.length) {
            return this.fetchSuggestionRequest()
        }
        this.suggestionsInMotion = false;
    },
    fetchSuggestionRequest: function () {
        livespell.ajax.send("WINSUGGEST", livespell.cache.suglist.join(livespell.str.chr(1)), spellWin.provider().Language, "", spellWin.providerID)
    },
    notify: function () {
        delete this.docs;
        this.docs = [];
        for (var i = 0; i < this.tokens.value.length; i++) {
            this.docs[i] = this.tokens.value[i].join("");
        }
        spellWin.tokenize();
        spellWin.provider().docUpdate(this.docs);
    },
    autoCorrectOnLoad: function () {
        var $$current_cookie = livespell.cookie.get("SPELL_AUTOCORRECT_USER");
        if (!$$current_cookie) {
            return false;
        }
        var $$mycmds = $$current_cookie.split(livespell.str.chr(1));
        for (var key in $$mycmds) {
            var mycmd = $$mycmds[key];
			if(!mycmd || !mycmd.length){return}
		
			try{
            var a_mycmd = mycmd.split("->");
            if (a_mycmd[1]) {
                var from = a_mycmd[0];
                var to = a_mycmd[1].split("#")[0];
                var toCase = a_mycmd[1].split("#")[1];
                livespell.cache.ignore[to.toLowerCase()] = true;
                this.tokens.findAndReplace(from, to, toCase);
            }
		}catch(e){}
        }
    },
    moveNext: function () {
        this.notify();
        try { window.focus(); } catch (e) { };

        var $$typo = this.FindNextTypo();
        if ($$typo === null) {
            return;
        }
        var suggestions = livespell.cache.suggestions[spellWin.provider().Language][$$typo];
        var reason = spellWin.currentReason;
        var newSentence = this.tokens.startsSentence(spellWin.currentDoc, spellWin.currentToken);

        //// Catch if not ready:
        var suggestionsPending = (reason !== "R" && reason !== "G" && !livespell.cache.suggestions[spellWin.provider().Language][$$typo]);
        if (suggestionsPending) {
            this.ui.disableAll();
            if (!this.suggestionsInMotion) {
                this.suggestionsInMotion = true
                spellWin.nextSuggestionChunk();
            }
            return setTimeout("spellWin.moveNext()", 1000)
        }
        var isGreen = reason == "G" || reason == "R";


		

		if (!isGreen){

			 var oCase = livespell.str.getCase($$typo);

		        if (oCase === 2) {
		            for (var j = 0; j < suggestions.length; j++) {
		                suggestions[j] = suggestions[j].toUpperCase();
		            }
			        } 


			 if (oCase === 1) {
		            for (var j = 0; j < suggestions.length; j++) {
		                suggestions[j] = livespell.str.toCaps(suggestions[j]);

		            }
			}
		}
					if (!suggestions || !suggestions.length || suggestions[0] === "") {
	   						suggestions = [];
							}
					var dsuggs = [];
				    for (j = 0; j < suggestions.length; j++) {
					dsuggs.safepush(suggestions[j] );
					}
					suggestions = dsuggs;


        $$("TextShow").innerHTML = this.tokens.appetureXHTML(spellWin.currentDoc, spellWin.currentToken, isGreen);
   
        var oS = $$("fldSuggestions");
        oS.options.length = 0


        if (reason == "G") {
	
            if (!suggestions || !suggestions.length || suggestions[0] === "") {
                suggestions = [];
                suggestions[0] = $$typo;
            }
            for (var i = 0; i < suggestions.length; i++) {
                suggestions[i] = livespell.str.toCase(suggestions[i], 0, true);
            }
        }

        if (!suggestions || !suggestions.length || suggestions[0] === "" || reason == "R") {
            oS.disabled = true;
        } else {
            oS.disabled = false;
            for (var i = 0; i < suggestions.length; i++) {
                if (reason === "X") {
                    oS.options[i] = (new Option(suggestions[i], "__*REG*__", i == 0, i == 0));
                } else {
                    oS.options[i] = (new Option(suggestions[i], suggestions[i], i == 0, i == 0));
                }
            }
        }


	
        this.ui.enable("btnIgnore,btnIgnoreAll,btnAddToDict,btnChange,btnChangeAll,btnAutoCorrect,btnShowOptions", true)

        if (reason == "X") {
            this.ui.enable("btnIgnoreAll,btnAddToDict,btnChangeAll,btnAutoCorrect", false)

        }
        this.ui.show("lMeaning", false);
        this.ui.enable("btnUndo", this.undo.bookmarks.length > 0)
        switch (reason) {
            case "C":
                $$("fldTextInputLab").innerHTML = spellWin.lang.fetch("REASON_CASE");
                break;
            case "R":

                $$("fldTextInputLab").innerHTML = spellWin.lang.fetch("REASON_REPEATED");
                oS.options.length = 0;
                oS.options[0] = (new Option(spellWin.lang.fetch("SUGGESTIONS_DELETE_REPEATED"), "__*DEL*__", false, true));
                this.ui.enable("btnIgnoreAll,btnAddToDict,btnChangeAll,btnAutoCorrect,fldSuggestions", false)
                break;
            case "G":


                $$("fldTextInputLab").innerHTML = spellWin.lang.fetch("REASON_GRAMMAR");
                this.ui.enable("btnIgnoreAll,btnAddToDict,btnChangeAll,btnAutoCorrect", false)
                break;


            default:
                if (reason === "B") {
                    $$("fldTextInputLab").innerHTML = spellWin.lang.fetch("REASON_BANNED");
                    if (spellWin.provider().Strict) {
                        this.ui.enable("btnIgnore,btnIgnoreAll,btnAddToDict", false)
                    }
                } else if (reason === "E") {
                    $$("fldTextInputLab").innerHTML = spellWin.lang.fetch("REASON_ENFORCED");
                    this.ui.enable("btnIgnore,btnIgnoreAll,btnAddToDict", false)
                } else {
                    $$("fldTextInputLab").innerHTML = spellWin.lang.fetch("REASON_SPELLING");
                }
                if (!suggestions.length || suggestions[0] == "") {
                    oS.options[0] = (new Option(spellWin.lang.fetch("SUGGESTIONS_NONE"), $$typo, false, true));
                    this.ui.enable("btnChange,btnChangeAll,btnAutoCorrect", false)
                } else {
                    if (spellWin.provider().ShowMeanings) {
                        this.ui.show("lMeaning", true);
                    }
                }
                break;
        }
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    },
    FindNextTypo: function () {
        for (var i = this.currentDoc; i < this.tokens.value.length; i++) {
            for (var j = (i === this.currentDoc ? this.currentToken : 0); j < this.tokens.value[i].length; j++) {
                if (this.tokens.isWord(i, j)) {
                    var word = this.tokens.value[i][j];
                    var spelltest = livespell.test.spelling(word, spellWin.provider().Language);
                    var unknown = spelltest !== true && spelltest !== false;
                    if (unknown) {

                        spellWin.regroup()
                        return null;
                    }
                    if (!spelltest) {
                        var reason = livespell.cache.reason[spellWin.provider().Language][word] ? livespell.cache.reason[spellWin.provider().Language][word] : "";
                        if (spellWin.provider().IgnoreAllCaps && livespell.test.ALLCAPS(word) && reason !== "B" && reason !== "E") {
                            spelltest = true;
                        }
                        if (spellWin.provider().IgnoreNumeric && (livespell.test.num(word)) && reason !== "B" && reason !== "E") {
                            spelltest = true;
                        }
                        if (!spellWin.provider().CaseSensitive && reason == "C") {
                            spelltest = true;
                        }
                    }
                    if (spellWin.provider().CheckGrammar && !spellWin.tokens.startsSentence(i, j) && j > 1 && spellWin.tokens.value[i][j].toUpperCase() === spellWin.tokens.value[i][j - 2].toUpperCase()) {
                        spelltest = false;
                        reason = "R";
                    }

                    if (spellWin.provider().CaseSensitive && spellWin.provider().CheckGrammar && spellWin.tokens.startsSentence(i, j) && livespell.test.lcFirst(word)) {
                        var strDoc = this.tokens.value[i].join(" ");
                        if (strDoc.indexOf(".") > 0 || strDoc.indexOf("!") > 0 || strDoc.indexOf("?") > 0 || strDoc.length > 50) {

                            spelltest = false;
                            reason = "G";
                        }
                    }
                    if (!spelltest) {
                        this.currentDoc = i;
                        this.currentToken = j;
                        this.currentReason = reason;
                        var nextWord = this.tokens.value[i][j];
                        return nextWord;
                    }
                }
            }
        };
        spellWin.ui.finished()
        return null;
    },
    sendInitialAJAXRequest: function () {

        livespell.ajax.send("WINSETUP", livespell.cache.wordlist[spellWin.providerID].join(livespell.str.chr(1)), spellWin.provider().Language, "5", spellWin.providerID)
    },
    tokenize: function () {
        for (var i = 0; i < this.docs.length; i++) {
            this.tokens.value[i] = livespell.str.tokenize(this.docs[i]);
        }
    },
    buildWordQue: function () {
        livespell.cache.wordlist[spellWin.providerID] = [];

        for (var i = 0; i < this.tokens.value.length; i++) {
            for (var j = 0; j < this.tokens.value[i].length; j++) {
                var spelltest = true
                spelltest = livespell.test.spelling(this.tokens.value[i][j], spellWin.provider().Language);
                var unknown = (spelltest !== true && spelltest !== false);
                if (this.tokens.isWord(i, j) && (!spelltest) && unknown) {
                    livespell.cache.wordlist[spellWin.providerID].safepush(this.tokens.value[i][j])
                }
            }
        }
    },
    handshake: function () {

        if (window.opener) {
            livespell = window.opener.livespell
        } else {
            livespell = dialogArguments.livespell
        }

        spellWin.providerID = (spellWin.querystring.get("instance"));
        spellWin.provider().spellWindowObject = spellWin;
    },
    setTheme: function () {
        var strTheme = spellWin.provider().CSSTheme;

        if (strTheme.length) {
            $$("theme").setAttribute("href", "themes/" + strTheme + "/dialog-window.css")
        }


    },
    hideButtons: function () {

        var arrHideButtons = (spellWin.provider().HiddenButtons.split(","))
        for (var i = 0; i < arrHideButtons.length; i++) {
            strBtn = arrHideButtons[i];
            try {
                oBtn = $$(strBtn);
                if (oBtn && oBtn.value) { oBtn.style.display = "none"; };
            } catch (e) { }
        }
    }
	,
    pickup: function () {

        this.docs = spellWin.provider().docPickup();
        //["hello ödd dias Dias hello doaisjd ajso dija yxxxxyyhgtrsgfrdt soldd soldd i USA UK  SGTSF! i id dias Dias hello doaisjd ajso dija osdij aoisjd oia jsd i dias i Dias I dias i i i usa i i ", "ijosdfj oisdjf oisd jf Dias"];
    },
    querystring: {
        get: function (request) {
            var query = window.location.search.substring(1);
            var vars = query.split("&");
            for (var i = 0; i < vars.length; i++) {
                var pair = vars[i].split("=");
                if (pair[0] == request) {
                    return pair[1];
                }
            }
        }

    },
    ui: {
        setGrammar: function () {
            spellWin.provider().CheckGrammar = $$("optSentence").checked;
            spellWin.moveNext();
        },
        setLoadingMessage: function () {
            $$("fldTextInputLab").innerHTML = "";
            setTimeout(this.setLoadingMessageTimed, 400);
        },
        setLoadingMessageTimed: function () {
            if ($$("fldTextInputLab").innerHTML !== "") { return };

            $$("fldTextInputLab").innerHTML = "<div id='ajaxLoader' ></div>";

        }
		,
        finished: function () {
            //spellWin.notify();
            this.show("optForm,SpellForm", false)
            if (!spellWin.provider().ShowSummaryScreen) {
                return spellWin.actions.done()
            }
            spellWin.allDone = true;
            $$("tDoc").innerHTML = spellWin.lang.fetch("DONESCREEN_FIELDS") + " " + spellWin.tokens.value.length;
            $$("tEdi").innerHTML = spellWin.lang.fetch("DONESCREEN_EDITS") + " " + spellWin.editcount;
            $$("tWrd").innerHTML = spellWin.lang.fetch("DONESCREEN_WORDS") + " " + spellWin.tokens.countWords();
            this.show("doneForm", true)
        },
        setupLanguageMenu: function () {
            $$("fldLanguage").options.length = 0;
            $$("fldLanguageMultiple").options.length = 0;
            var ffound = false
            for (l = 0; l < livespell.cache.langs.length; l++) {
                $$("fldLanguage")[l] = new Option(livespell.cache.langs[l], livespell.cache.langs[l], false, spellWin.provider().Language == livespell.cache.langs[l]);
                $$("fldLanguageMultiple")[l] = new Option(livespell.cache.langs[l], livespell.cache.langs[l], false, spellWin.provider().Language == livespell.cache.langs[l]);
                if (spellWin.provider().Language === livespell.cache.langs[l]) {
                    ffound = true;
                }
            }
            $$("fldLanguage")[l] = new Option(spellWin.lang.fetch("LANGUAGE_MULTIPLE"), spellWin.lang.fetch("LANGUAGE_MULTIPLE"), false, false);
            if (ffound !== true) {
                $$("fldLanguage").options.add(new Option(spellWin.provider().Language, spellWin.provider().Language, true, true), 0);
                $$("fldLanguage").selectedIndex = 0;
            }
        },
        lookupMeaning: function () {
var word	=  spellWin.ui.getMenuValue("fldSuggestions");
if(word=="__*REG*__"){word = "registration"}

	        var url = spellWin.provider().MeaningProvider.replace("{word}", word)
         
 			window.open( url)
        },
        disableAll: function () {
            this.showEdit(false);
            this.enable("btnIgnore,btnIgnoreAll,btnAddToDict,btnChange,btnChangeAll,btnAutoCorrect,btnUndo,btnShowOptions", false)
        },
        enable: function (elements, flag) {
            if (!elements.join) {
                var elements = elements.split(",")
            }
            for (var i = 0; i < elements.length; i++) {
                try {
                    $$(elements[i]).disabled = !flag;
                } catch (e) { }
            }
        },
        show: function (elements, flag) {
            if (!elements.join) {
                var elements = elements.split(",")
            }
            for (var i = 0; i < elements.length; i++) {
                $$(elements[i]).style.display = flag ? "block" : "none";
            }
        },
        editingNow: false,
        showEdit: function (flag) {
            if (this.editingNow == flag) {
                return null;
            }

            $$("fldTextInput").value = flag ? spellWin.tokens.appetureText(spellWin.currentDoc, spellWin.currentToken) : "";

            this.editingNow = flag;
            this.show("TextShow", !flag);
            this.show("btnUndoManualEdit", flag);
            this.enable("btnIgnore,btnIgnoreAll,btnAddToDict,btnChangeAll,btnAutoCorrect,btnShowOptions,fldSuggestions", !flag)
            if (flag) {
                this.enable('btnChange', true)
                $$('fldTextInput').focus();
            } else {
                spellWin.moveNext()
            }
            return null;
        },
        getMenuValue: function (id) {
            var o = $$(id)
            if (!o.multiple) {
                if (o.selectedIndex === null || o.selectedIndex < 0) {
                    return ""
                }
                return o.options[o.selectedIndex].value;
            } else {
                var selVals = new Array();
                for (var i = 0; i < o.length; i++) {
                    if (o.options[i].selected) {
                        selVals.push(o.options[i].value);
                    }
                }
                return selVals.join(",")
            }
        }
    }
}


if (!Array.safepush) {
    Array.prototype.safepush = function (value) {
        for (var i = 0; i < this.length; i++) {
            if (this[i] === value) {
                return false;
            }
        }
        this.push(value);
        return true;
    }
}
if (!Array.push) {
    Array.prototype.push = function () {
        var n = this.length >>> 0;
        for (var i = 0; i < arguments.length; i++) {
            this[n] = arguments[i];
            n = n + 1 >>> 0;
        }
        this.length = n;
        return n;
    };
}
if (!Array.pop) {
    Array.prototype.pop = function () {
        var n = this.length >>> 0,
			value;
        if (n) {
            value = this[--n];
            delete this[n];
        }
        this.length = n;
        return value;
    };
}
if (!Array.shift) {
    Array.prototype.shift = function () {
        firstElement = this[0];
        this.reverse();
        this.length = Math.max(this.length - 1, 0);
        this.reverse();
        return firstElement;
    }
}

$$ = function (id) {
    return document.getElementById(id);
}


