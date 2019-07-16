if (typeof (LiveSpellInstance) == "undefined") {

    function LiveSpellInstance($setup) {

        livespell.spellingProviders.push(this);

      
        this.Fields = "ALL";
        this.IgnoreAllCaps = true;
        this.IgnoreNumeric = true;
        this.CaseSensitive = true;
        this.CheckGrammar = true;
        this.Language = "English (International)";
        this.MultiDictionary = false;
        this.UserInterfaceLanguage = "en";
        this.CSSTheme = "classic";
        this.SettingsFile = "default-settings";
        this.ServerModel = "";
        this.Delay = 888;
        this.WindowMode = "modal";
        this.Strict = true;
        this.ShowSummaryScreen = true;
        this.ShowMeanings = true;
        this.FormToSubmit = "";
        this.MeaningProvider = "http://www.thefreedictionary.com/{word}";
        this.UndoLimit = 20;
        this.HiddenButtons = "";
        this.CustomOpener = null;
        this.CustomOpenerClose = null;


        ///
        this.UserSpellingInitiated = false;
        this.UserSpellingComplete = false;
        ///
        this.SetUserInterfaceLanguage = function (l) {
            this.UserInterfaceLanguage = l;
            livespell.lang.load(l)
        }
        this.isUniPacked = false;
        this.FieldType = function (id) {

            var oField = document.getElementById(id);
            var TYPE = oField.nodeName.toUpperCase();
            if (TYPE == "INPUT" || TYPE == "TEXTAREA") {
                return "value"
            }
            if (TYPE == "IFRAME") {

                return "iframe"
            }
            return "innerHTML";
        }

        this.docUpdate = function (docs) {
            var fieldIds = this.arrCleanFields();
            this.onUpdateFields(fieldIds);

            for (var i = 0; i < fieldIds.length; i++) {
                var id = fieldIds[i];
                var t = this.FieldType(id);
                var oField = document.getElementById(id);
                if (docs[i] !== oField[t]) {

                    if (t === "iframe") {
                        var myFrame = oField;
                        var oDoc = livespell.getIframeDocument(oField);
                        var oBody = oDoc.body;

                        if (oBody.innerHTML != docs[i]) {
                            oBody.innerHTML = docs[i]

                        };
                    } else {
                        oField[t] = docs[i];
                    }
                    if (t === "value" && livespell.insitu.proxyDOM(id)) {
                        livespell.insitu.updateProxy(id);
                        livespell.insitu.checkNow(id, this.id())
                    }
                }
            }
        }

        this.docPickup = function () {
            var fieldIds = this.arrCleanFields();
            var docs = []
            for (var i = 0; i < fieldIds.length; i++) {
                var id = fieldIds[i];

                var oField = document.getElementById(id);
                var t = this.FieldType(id);
                if (t === "iframe") {

                    var oDoc = livespell.getIframeDocument(oField);
                    var val = oDoc.body.innerHTML;

                } else {
                    var val = oField[t];
                    val = livespell.str.stripTags(val);
                }
                docs[i] = val;
            }

            return docs;
        }

        this.CheckInSitu = function () {
            this.UserSpellingInitiated = true;
            livespell.context.renderCss(this.CSSTheme);

            livespell.insitu.checkNow(this.arrCleanFields(), this.id())
        }
        this.FieldModified = function () {

            try {
                if (this.spellWindowObject.isStillOpen()) {
                    this.spellWindowObject.resumeAfterEditing();
                }
            } catch (e) { }
        }


        this.setFieldListeners = function () {

            var fieldIds = this.arrCleanFields();
            {
                for (var i = 0; i < fieldIds.length; i++) {
                    var id = fieldIds[i];
                    var oField = document.getElementById(id);
                    if (!oField["livespell__listener_" + this.id()]) {
                        oField["livespell__listener_" + this.id()] = true;
                        var ty = this;
                        var fn = function () { ty.FieldModified(); }

                        var t = this.FieldType(id);

                        if (t === "value" && livespell.insitu.proxyDOM(id)) {

                            livespell.events.add(livespell.insitu.proxyDOM(id), "blur", fn, false)

                        } else {
                            livespell.events.add(oField, "change", fn, false)

                        }



                    }
                }
            }
        }



        this.CheckInWindow = function () {
            this.UserSpellingInitiated = true;
            this.SetUserInterfaceLanguage(this.UserInterfaceLanguage);
            this.onDialogOpen();

            var webkit = /webkit/.test(navigator.userAgent.toLowerCase())

            if (this.CustomOpener) {
                return this.CustomOpener(this.url())
            }
            if (this.WindowMode.toLowerCase() == "modal" && window.showModalDialog && !webkit) {
                window.showModalDialog(this.url(), window, "center:1;dialogheight:290px;dialogwidth:460px;resizable:0;scrollbars:0;scroll:0;location:0");
            }
            else if (this.WindowMode.toLowerCase() == "modeless" && window.showModelessDialog && !webkit) {
                window.showModelessDialog(this.url(), window, "center:1;dialogheight:290px;dialogwidth:460px;resizable:0;scrollbars:0;scroll:0;location:0");
            }
            else {
                window.open(this.url(), "spelldialog", "width=460,height=290,scrollbars=no,resizable=no;centerscreen=yes;location=no;tolbar=no;menubar=no", false);
            }

        }
        this.url = function () {
            var strout = livespell.installPath + "dialog.html";
            strout += "?instance=" + this.id();
            return strout;
        }

        this.m_ayt = [];
        this.m_ayt_timeout = null;
        this.m_AYTAjaxInervalHandler = function () {
            var fieldIds = this.m_ayt;
            if (!fieldIds.length) { return; };
            for (var i = 0; i < fieldIds.length; i++) {

                var id = fieldIds[i];

                var oChild = $$(id)

                if (oChild) {
                    var found = false;
                    if (oChild.isCurrentAjaxImplementation !== true) {
                        oChild.isCurrentAjaxImplementation = true
                        found = true;
                    }
                }
            }
            if (found) { this.ActivateAsYouType(); }
        }
        this.setAYTAjaxInervalHandler = function () {

            clearInterval(this.m_ayt_timeout);
            var t = this;
            var f = function () { t.m_AYTAjaxInervalHandler() }
            setInterval(f, 1000);
        }
        this.ActivateAsYouTypeOnLoad = function () {
            livespell.context.renderCss(this.CSSTheme);
            var o = this;
            var fn = function () { o.ActivateAsYouType() }
            livespell.events.add(window, "load", fn, false);
        }
        this.ActivateAsYouType = function () {

            if (livespell.test.browserWebKit()) { return; }

            this.SetUserInterfaceLanguage(this.UserInterfaceLanguage);
            livespell.context.renderCss(this.CSSTheme);
            fieldIds = this.arrCleanFields();
            this.AsYouTypeIsActive = true;
            for (var i = 0; i < fieldIds.length; i++) {
                var id = fieldIds[i];
                if ($$(id).nodeName.toLowerCase() == "textarea") {

                    oField = livespell.insitu.createProxy(id);
                    if (!oField) { return }
                    oField.setAttribute("autocheck", true);
                    oField.autocheck = true;
                    oField.autocheckProvider = this.id();
                    oChild = $$(id);

                    oChild.isCurrentAjaxImplementation = true;
                    this.m_ayt.safepush(id.replace("livespell____", ""));
                }
            }


            this.CheckInSitu();
            this.setAYTAjaxInervalHandler();
        }
        this.AsYouTypeIsActive = false;

        this.PauseAsYouType = function () {
            fieldIds = this.arrCleanFields();
            for (var i = 0; i < fieldIds.length; i++) {
                var id = fieldIds[i];
                livespell.insitu.destroyProxy(id);
                this.AsYouTypeIsActive = false;
                //var ofield = livespell.insitu.proxyDOM(id);
                //oField.autocheck = false;
            }
        }


        this.AjaxDidYouMean = function (input) {
            livespell.ajax.send("APIDYM", input, this.Language, "", this.id());
        }
        this.AjaxSpellCheck = function (input, makeSuggestions) {
            makeSuggestions = makeSuggestions !== false;
            var wordstocheck = input.join ? input : [input];
            var allFound = true
            for (var i = 0; i < wordstocheck.length && allFound; i++) {
                var word = wordstocheck[i];
                allFound = allFound && livespell.test.fullyCached(word, this.Language, makeSuggestions);
                if (!allFound) {
                    //	alert(word + " broke it")
                }
            }
            if (allFound) {
                this.onSpellCheckFromCache(input, makeSuggestions);
                return;
            }
            if (input.join) {
                input = input.join(livespell.str.chr(1))
                livespell.ajax.send("APISPELLARRAY", input, this.Language, makeSuggestions ? "" : "NOSUGGEST", this.id());
            } else {
                livespell.ajax.send("APISPELL", input, this.Language, makeSuggestions ? "" : "NOSUGGEST", this.id());
            }
        };
        this.AjaxSpellCheckArray = function (input, makeSuggestions) {
            this.AjaxSpellCheck(input, makeSuggestions);
        }
        this.onSpellCheck = function (input, spelling, reason, suggestions) { }
        this.onDidYouMean = function (suggestion, origional) { }
        this.onSpellCheckFromCache = function (input, makeSuggestions) {
            var isArray = input.join;
            if (!isArray) {
                input = [input];
            }
            var outInput = input;
            var outSpellingOk = []
            var outSuggestions = []
            var outReason = []
            for (var i = 0; i < input.length; i++) {
                var word = input[i]
                outSpellingOk[i] = livespell.test.spelling(word, this.Language);
                outReason[i] = outSpellingOk[i] ? "-" : livespell.cache.reason[this.Language][word];
                outSuggestions[i] = makeSuggestions ? livespell.cache.suggestions[this.Language][word] : [];
            }
            if (isArray) {
                this.onSpellCheck(outInput, outSpellingOk, outReason, outSuggestions)
            } else {
                this.onSpellCheck(outInput[0], outSpellingOk[0], outReason[0], outSuggestions[0])
            }
        }
        this.arrCleanFields = function () {
            // check each exixts first
            var F = this.Fields;

            var isString = F.split;
            if (isString) {
                F = F.replace(/\s/g, "").split(",");
            }


            var out = new Array;

            for (var j = 0; j < F.length; j++) {

                var oid = F[j];


                var found = false;
                if (oid.toUpperCase() === "TEXTAREAS" || oid.toUpperCase() === "ALL") {

                    var out = [];
                    var AF = livespell.insitu.allTextAreas();

                    for (var i = 0; i < AF.length; i++) {
                        out.safepush(AF[i]);
                        found = true;
                    }
                }
                if (oid.toUpperCase() === "TEXTINPUTS" || oid.toUpperCase() === "ALL") {
                    var AF = livespell.insitu.allTextInputs();

                    for (var i = 0; i < AF.length; i++) {
                        out.safepush(AF[i]);
                        found = true;
                    }
                }
                if (oid.toUpperCase() === "EDITORS" || oid.toUpperCase() === "ALL") {

                    var AF = livespell.insitu.allEditors();
                    for (var i = 0; i < AF.length; i++) {
                        out.safepush(AF[i]);
                        found = true;
                    }
                }
                if (oid.toUpperCase().split(":")[0] == "IFRAME") {
                    var e;

                    var frameindex = Number(oid.split(":")[1]);
                    if (frameindex < document.getElementsByTagName("iframe").length) {
                        myFrame = document.getElementsByTagName("iframe")[frameindex]
                        if (!myFrame.id) { myFrame.id = "livespell_IFRAME_id_" + frameindex }
                        out.safepush(myFrame.id);
                        found = true;
                    }
                }

                if (!found) {

                    if (document.getElementById(oid)) {

                        out.safepush(oid)
                    }
                    else if (oid.id) {
                        out.safepush(oid)
                    }
                    else if (oid.name) {
                        oid.id = "livespell____" + oid.name;
                        out.safepush("livespell____" + oid.name)
                    }
                    else if (document.getElementsByName(oid).length == 1) {
                        document.getElementsByName(oid)[0].id = "livespell____" + oid;
                        out.safepush("livespell____" + oid)
                    }
                }
            }

            return out;
        }
        this.id = function () {
            for (var i = 0; i < livespell.spellingProviders.length; i++) {
                if (this === livespell.spellingProviders[i]) {
                    return i
                }
            }
        }
        this.recieveWindowSpell = function () {
            try {
                this.spellWindowObject.nextSuggestionChunk()
            } catch (e) { }
        }
        this.recieveWindowSetup = function () {
            try {
                this.spellWindowObject.ui.setupLanguageMenu();
                this.spellWindowObject.nextSuggestionChunk();
                this.spellWindowObject.moveNext();
            } catch (e) { }
        };
        this.recieveContextSpell = function () {
            var myFields = this.arrCleanFields();
            for (var i = 0; i < myFields.length; i++) {
                livespell.insitu.renderProxy(myFields[i], this.id())
            }
        }





        this.SpellButton = function (insitu, text, Class, style) {
            if (!insitu) { insitu = false };
            if (!text) { text = "Spell Check" };
            if (!Class) { Class = ""; };
            if (!style) { style = ""; };

            var holder = document.createElement("span");
            var o = document.createElement("input");
            o.setAttribute("type", "button");
            o.type = "button";
            o.setAttribute("value", text);
            o.value = text;
            o.setAttribute("Class", Class);
            o.className = Class;
            o.setAttribute("style", style);
            if (insitu) {
                o.setAttribute("onclick", " livespell.spellingProviders[" + this.id() + "].CheckInSitu()");
            } else {
                o.setAttribute("onclick", " livespell.spellingProviders[" + this.id() + "].CheckInWindow()");
            }
            holder.appendChild(o);
            return (holder.innerHTML)
        }
        this.SpellLink = function (insitu, text, Class, style) {
            if (!insitu) { insitu = false };
            if (!text) { text = "Spell Check" };
            if (!Class) { Class = ""; };
            if (!style) { style = ""; };

            var holder = document.createElement("span");
            var o = document.createElement("a");
            o.innerHTML = text;
            if (insitu) {
                o.setAttribute("href", "javascript:livespell.spellingProviders[" + this.id() + "].CheckInSitu()");
            } else {
                o.setAttribute("href", "javascript:livespell.spellingProviders[" + this.id() + "].CheckInWindow()");
            }
            o.setAttribute("Class", Class);
            o.className = Class;
            o.setAttribute("style", style);
            holder.appendChild(o);
            return (holder.innerHTML)
        }
        this.SpellImageButton = function (insitu, image, rollover, text, Class, style) {

            if (!insitu) { insitu = false };
            if (!text) { text = "Spell Check" };
            if (!Class) { Class = ""; };
            if (!image) { image = "themes/buttons/spellicon.gif"; rollover = "themes/buttons/spelliconover.gif" };
            if (!style) { style = ""; };

            var holder = document.createElement("span");
            var o = document.createElement("img");
            o.setAttribute("alt", text);
            o.alt = text;
            o.setAttribute("src", livespell.installPath + image);



            o.src = livespell.installPath + image;
            o.setAttribute("border", "0");
            o.setAttribute("onmouseover", "this.src='" + livespell.installPath + rollover + "'");

            if (rollover) {
                o.setAttribute("onmouseout", "this.src='" + livespell.installPath + image + "'");
            }
            if (insitu) {
                o.setAttribute("onclick", "livespell.spellingProviders[" + this.id() + "].CheckInSitu()");
            } else {
                o.setAttribute("onclick", "livespell.spellingProviders[" + this.id() + "].CheckInWindow()");
            }
            o.setAttribute("Class", Class);
            o.className = Class;
            o.setAttribute("style", "pointer:cursor; " + style);
            holder.appendChild(o);

            return (holder.innerHTML)
        }




        this.DrawSpellImageButton = function (insitu, image, rollover, text, Class, style) {
            livespell.context.renderCss(this.CSSTheme);
            document.writeln(this.SpellImageButton(insitu, image, rollover, text, Class, style));
        }

        this.DrawSpellLink = function (insitu, text, Class, style) {
            livespell.context.renderCss(this.CSSTheme);
            document.writeln(this.SpellLink(insitu, text, Class, style))
        }

        this.DrawSpellButton = function (insitu, text, Class, style) {
            livespell.context.renderCss(this.CSSTheme);
            document.writeln(this.SpellButton(insitu, text, Class, style))
        }
        this.__SubmitForm = function () {

            if (!this.FormToSubmit.length) { return; };
            var e;
            try {
                $$(this.FormToSubmit).submit()
            } catch (e) { }
        };

        this.onDialogCompleteNET = function () {

            if (this.UniqueIDNetPostBack != "") {
                if (window.__doPostBack) {
                    window.__doPostBack(this.UniqueIDNetPostBack, this.UniqueIDNetPostBack);
                }
            }
        };
        this.UniqueIDNetPostBack = "";
        this.onDialogOpen = function () { };
        this.onDialogComplete = function () { };
        this.onDialogCancel = function () { };
        this.onDialogClose = function () { };
        this.onChangeLanguage = function (Language) { };
        this.onIgnore = function (Word) { };
        this.onIgnoreAll = function (Word) { };
        this.onChangeWord = function (From, To) { };
        this.onChangeAll = function (From, To) { };
        this.onLearnWord = function (Word) { };
        this.onLearnAutoCorrect = function (From, To) { };
        this.onUpdateFields = function (arrFieldIds) { };


    }
}

if (typeof (livespell) == "undefined") {
    livespell = {
        liveProxys: [],
        installPath: "",
        spellingProviders: [],
        inlineblock: function () {
            return window.getComputedStyle ? "inline-block" : "inline";
        },
        heartbeat: function () {

            //		livespell.liveProxys;  // do this liv
            var DesiredActive = new Array();

            for (var p = 0; p < livespell.spellingProviders.length; p++) {
                var provider = livespell.spellingProviders[p];

                if (provider.AsYouTypeIsActive) {
                    var flist = provider.arrCleanFields();

                    for (var f = 0; f < flist.length; f++) {
                        var id = flist[f];

                        DesiredActive.safepush(id);
                        if (!document.getElementById(id + livespell.insitu._FIELDSUFFIX)) {

                            provider.ActivateAsYouType()
                        }
                    }
                }
            }



            var divs = document.getElementsByTagName("div");
            for (var i = 0; i < divs.length; i++) {
                thisdiv = divs[i];
                if (thisdiv.isLiveSpellProxy && thisdiv.id) {
                    var shouldBeThere = false
                    var id = thisdiv.id.replace(livespell.insitu._FIELDSUFFIX, "")
                    for (j = 0; j < DesiredActive.length && !shouldBeThere; j++) {
                        if (DesiredActive[j] == id) { shouldBeThere = true }
                    }
                    if (!shouldBeThere) {

                        livespell.insitu.destroyProxy(id);
                    }

                }

            }


        },
        getIframeDocument: function (myFrame) {

            var oDoc = myFrame.contentDocument ? myFrame.contentDocument : myFrame.document;
            var oBody = oDoc.body;

            var isEditable = oBody.contentEditable === "true" || oBody.contentEditable === true || oBody.designMode == 'on' || oBody.designMode == 'On' || oBody.designMode == 'ON' || oBody.designMode === true || oBody.designMode === "true" || oDoc.contentEditable === true || oDoc.designMode == 'on' || oDoc.designMode === true || oDoc.designMode === "true";

            if (isEditable) {
                return oDoc
            }

            myFrame = myFrame.contentWindow ? myFrame.contentWindow : myFrame;



            if (myFrame.frames.length) {

                for (var i = 0; i < myFrame.frames.length; i++) {
                    var mySubFrame = myFrame.frames[i];

                    var oSubDoc = mySubFrame.contentDocument ? mySubFrame.contentDocument : mySubFrame.document;
                    var oBody = oSubDoc.body;

                    var isEditable = oBody.spellcheck === "false" || oBody.spellcheck === false || oBody.contentEditable === "true" || oBody.contentEditable === true || oBody.designMode == 'on' || oBody.designMode == 'On' || oBody.designMode == 'ON' || oBody.designMode === true || oBody.designMode === "true" || oDoc.contentEditable === true || oDoc.designMode == 'on' || oDoc.designMode === true || oDoc.designMode === "true";

                    if (isEditable) {

                        return oSubDoc
                    }
                }

                var isEditable = oBody.spellcheck === "false" || oBody.spellcheck === false;

                if (isEditable) {
                    return oDoc
                }

            }



            return null;


        },
        lang: {

            fetch: function (providerID, index) {

                var lang = livespell.spellingProviders[providerID].UserInterfaceLanguage;

                try {
                    return this[lang][this[index]];
                } catch (e) {
                    try {
                        return this["en"][this[index]];
                    } catch (e) {
                        return index;
                    }
                }


            },

            load: function (lang) {
                if (!(livespell.installPath)) { return; }
                var idname = "__livespell__translations__" + lang;

                var fileref = $$(idname)
                if (fileref) {
                    //				fileref.setAttribute("src", livespell.installPath+"translations/"+lang+".js");	
                } else {
                    fileref = document.createElement("script");
                    fileref.setAttribute("id", idname);
                    fileref.id = idname;

                    fileref.setAttribute("type", "text/javascript");
                    fileref.setAttribute("src", livespell.installPath + "translations/" + lang + ".js");

                    document.getElementsByTagName("head")[0].appendChild(fileref);

                }
            },


            BTN_ADD_TO_DICT: 0,
            BTN_AUTO_CORECT: 1,
            BTN_CANCEL: 2,
            BTN_CHANGE: 3,
            BTN_CHANGE_ALL: 4,
            BTN_CLEAR_EDIT: 5,
            BTN_CLOSE: 6,
            BTN_IGNORE_ALL: 7,
            BTN_IGNORE_ONCE: 8,
            BTN_OK: 9,
            BTN_OPTIONS: 10,
            BTN_RESET: 11,
            BTN_UNDO: 12,
            DONESCREEN_EDITS: 13,
            DONESCREEN_FIELDS: 14,
            DONESCREEN_MESSAGE: 15,
            DONESCREEN_WORDS: 16,
            LABEL_LANGAUGE: 17,
            LABEL_SUGGESTIONS: 18,
            LANGUAGE_MULTIPLE: 19,
            LANGUAGE_MULTIPLE_INSTRUCTIONS: 20,
            LOOKUP_MEANING: 21,
            MENU_APPLY: 22,
            MENU_CANCEL: 23,
            MENU_DELETEBANNED: 24,
            MENU_DELETEREPEATED: 25,
            MENU_IGNORE: 26,
            MENU_IGNOREALL: 27,
            MENU_LANGUAGES: 28,
            MENU_LEARN: 29,
            MENU_NOSUGGESTIONS: 30,
            OPT_CASE_SENSITIVE: 31,
            OPT_ENTRIES: 32,
            OPT_IGNORE_CAPS: 33,
            OPT_IGNORE_NUMERIC: 34,
            OPT_PERSONAL_AUTO_CURRECT: 35,
            OPT_PERSONAL_DICT: 36,
            OPT_SENTENCE_AWARE: 37,
            REASON_BANNED: 38,
            REASON_CASE: 39,
            REASON_ENFORCED: 40,
            REASON_GRAMMAR: 41,
            REASON_REPEATED: 42,
            REASON_SPELLING: 43,
            SUGGESTIONS_DELETE_REPEATED: 44,
            SUGGESTIONS_NONE: 45,
            USRBTN_SPELL_CHECK: 46,
            WIN_TITLE: 47


        }, constants: {
            _IFRAME: "livespell___ajax_frame",
            _AJAXFORM: "livespell___ajax_form"
        }, ajax: {
            renderIframe: function (postURL) {
                if ($$(livespell.constants._IFRAME)) {
                    return;
                }
                var n = document.createElement("span");
                n.innerHTML = "<iframe id='" + livespell.constants._IFRAME + "' style='display:none;width:1px;height:1px;' src='about:blank' name='" + livespell.constants._IFRAME + "' ></iframe>"
                document.body.appendChild(n);
                var f = document.createElement("form");
                f.setAttribute("method", "post");
                f.setAttribute("action", postURL);
                f.setAttribute("id", livespell.constants._AJAXFORM);
                f.setAttribute("name", livespell.constants._AJAXFORM);
                f.setAttribute("target", livespell.constants._IFRAME);
                var fieldList = ["command", "args", "lan", "note", "script", "sender"];
                for (var i = 0; i < fieldList.length; i++) {
                    var fieldName = fieldList[i];
                    n = document.createElement("input");
                    n.setAttribute("type", "hidden");
                    n.setAttribute("name", fieldName);
                    n.setAttribute("id", "livespell___ajax_form_" + fieldName);
                    f.appendChild(n);
                    n = null;
                }
                document.body.appendChild(f);
            }, resend: function () {
                livespell.ajax.send(livespell.cache.ajaxrequest.cmd, livespell.cache.ajaxrequest.args, livespell.cache.ajaxrequest.lan, livespell.cache.ajaxrequest.note, livespell.cache.ajaxrequest.sender);
            }, send: function (cmd, args, lan, note, sender) {

                livespell.cache.ajaxrequest = {};
                livespell.cache.ajaxrequest.cmd = cmd;
                livespell.cache.ajaxrequest.args = args;
                livespell.cache.ajaxrequest.lan = lan;
                livespell.cache.ajaxrequest.note = note;
                livespell.cache.ajaxrequest.sender = sender;

                var oSender = livespell.spellingProviders[sender];
                var serverModel = oSender.ServerModel.toLowerCase()
                var posturl = livespell.installPath + "core/"
                if (serverModel === "asp.net") { posturl += "default.ashx" }
                else if (serverModel === "asp") { posturl += "default.asp" }
                else if (serverModel !== "") { posturl += "index." + serverModel }

                var settingsfile = livespell.spellingProviders[sender].SettingsFile;

                var hasajax = false;
                var xhr;
                try {
                    xhr = new ActiveXObject('Msxml2.XMLHTTP');
                    hasajax = true;
                }
                catch (e) {
                    try {
                        xhr = new ActiveXObject('Microsoft.XMLHTTP');
                        hasajax = true;
                    }
                    catch (e2) {
                        try {
                            xhr = new XMLHttpRequest();
                            hasajax = true;
                        }
                        catch (e3) {
                            xhr = false;
                            hasajax = false;
                        }
                    }
                }
                //	hasajax = false; to test iframe
                if (hasajax) {
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState == 4) {
                            if (xhr.status == 200) {
                                livespell.ajax.pickup(xhr.responseText);
                            }
                            else { /*handel error*/
                            }
                        }
                    };
                    var params = "command=" + (cmd);
                    params += "&args=" + escape(args);
                    params += "&lan=" + escape(lan);
                    params += "&note=" + escape(note);
                    params += "&sender=" + escape(sender);
                    params += "&settingsfile=" + escape(settingsfile);


                    xhr.open("POST", posturl, true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=ISO-8859-1");
                    xhr.setRequestHeader("Content-length", params.length);
                    xhr.setRequestHeader("Connection", "close");
                    xhr.send(params);
                } else {
                    var e;
                    livespell.ajax.renderIframe(posturl);
                    try {
                        window.frames[livespell.constants._IFRAME].document.location = "about:blank";
                    } catch (e) { /**/
                    }


                    $$(livespell.constants._AJAXFORM + "_command").value = cmd;
                    $$(livespell.constants._AJAXFORM + "_args").value = args;
                    $$(livespell.constants._AJAXFORM + "_lan").value = lan;
                    $$(livespell.constants._AJAXFORM + "_note").value = note;
                    $$(livespell.constants._AJAXFORM + "_sender").value = sender;
                    $$(livespell.constants._AJAXFORM + "_script").value = "true";
                    $$(livespell.constants._AJAXFORM).submit();
                }
            },
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            pickupIframe: function (strHTML) {

                this.pickup(strHTML.split("<script")[0]);
            },
            pickup: function (strHTML) {

                if (strHTML.indexOf(livespell.str.chr(5)) === -1) {
                    setTimeout(livespell.ajax.resend, 5000);
                    return
                }

                var arrResult = (strHTML).split(livespell.str.chr(5));
                var command = arrResult[0];
                var vSender = Number(arrResult[1]);
                var oSender = livespell.spellingProviders[vSender]
                var vLang = oSender.Language
                if (!livespell.cache.suggestions[vLang]) {
                    livespell.cache.suggestions[vLang] = [];
                }
                if (!livespell.cache.spell[vLang]) {
                    livespell.cache.spell[vLang] = [];
                }
                if (!livespell.cache.reason[vLang]) {
                    livespell.cache.reason[vLang] = [];
                }
                if (command === "CTXSPELL") {
                    var t = arrResult[2].split("");
                    var r = arrResult[3].split("");
                    for (var i = 0; i < t.length; i++) {
                        livespell.cache.reason[vLang][livespell.cache.wordlist[vSender][i]] = (r[i]);
                        livespell.cache.spell[vLang][livespell.cache.wordlist[vSender][i]] = (t[i] === "T");
                    }
                    oSender.recieveContextSpell()
                } else if (command === "CTXSUGGEST") {
                    var newSuggestions = arrResult[2].split(livespell.str.chr(2));
                    livespell.cache.suggestions[vLang][livespell.cache.suggestionrequest.word] = newSuggestions;
                    for (var j = 0; j < newSuggestions.length; j++) {
                        livespell.cache.spell[vLang][newSuggestions[j]] = true;
                        var sug_each_word = newSuggestions[j].replace(/\-/g, " ").split(" ")
                        for (var k = 0; k < sug_each_word.length; k++) {
                            livespell.cache.spell[vLang][sug_each_word[k]] = true;
                        }
                    }
                    if (arrResult[3] && arrResult[3].length) {
                        livespell.cache.langs = (arrResult[3].split(livespell.str.chr(2)));
                    }
                    livespell.context.showMenu(livespell.cache.suggestionrequest.id, livespell.cache.suggestionrequest.word, livespell.cache.suggestionrequest.reason, livespell.cache.suggestionrequest.providerID);
                } else if (command === "WINSUGGEST") {
                    var Suggestions = arrResult[2].split(livespell.str.chr(1));
                    for (var i = 0; i < livespell.cache.suglist.length; i++) {
                        var newSuggestions = Suggestions[i].split(livespell.str.chr(2));
                        livespell.cache.suggestions[vLang][livespell.cache.suglist[i]] = newSuggestions;
                        for (var j = 0; j < newSuggestions.length; j++) {
                            livespell.cache.spell[vLang][newSuggestions[j]] = true;
                            var sug_each_word = newSuggestions[j].replace(/\-/g, " ").split(" ")
                            for (var k = 0; k < sug_each_word.length; k++) {
                                livespell.cache.spell[vLang][sug_each_word[k]] = true;
                            }
                        }
                    }
                    oSender.recieveWindowSpell()
                } else if (command === "WINSETUP") {
                    var Suggestions = arrResult[4].split(livespell.str.chr(1));
                    var t = arrResult[2].split("");
                    var r = arrResult[3].split(livespell.str.chr(1));
                    for (var i = 0; i < t.length; i++) {
                        livespell.cache.reason[vLang][livespell.cache.wordlist[vSender][i]] = (r[i]);
                        livespell.cache.spell[vLang][livespell.cache.wordlist[vSender][i]] = (t[i] === "T");
                        if (!livespell.cache.spell[vLang][livespell.cache.wordlist[vSender][i]] && i < Suggestions.length) {
                            var newSuggestions = Suggestions[i].split(livespell.str.chr(2));
                            livespell.cache.suggestions[vLang][livespell.cache.wordlist[vSender][i]] = newSuggestions;
                            for (var j = 0; j < newSuggestions.length; j++) {
                                livespell.cache.spell[vLang][newSuggestions[j]] = true;
                                var sug_each_word = newSuggestions[j].replace(/\-/g, " ").split(" ")
                                for (var k = 0; k < sug_each_word.length; k++) {
                                    livespell.cache.spell[vLang][sug_each_word[k]] = true;
                                }
                            }
                        }
                    }
                    if (arrResult[5] && arrResult[5].length) {
                        livespell.cache.langs = (arrResult[5].split(livespell.str.chr(2)));
                    }
                    oSender.recieveWindowSetup()
                } else if (command === "APIDYM") {
                    var Suggestion = arrResult[3]
                    var Origional = arrResult[2]
                    oSender.onDidYouMean(Suggestion, Origional);
                }
                else if (command === "APISPELL" || command === "APISPELLARRAY") {
                    var doSuggest = arrResult[4].length > 0;
                    var Suggestions = arrResult[4].split(livespell.str.chr(1));
                    var t = arrResult[2].split("");
                    var r = arrResult[3].split(livespell.str.chr(1));
                    var outInput = arrResult[5].split(livespell.str.chr(1));
                    var outSpellingOk = []
                    var outSuggestions = []
                    var outReason = []
                    for (var i = 0; i < outInput.length; i++) {
                        livespell.cache.reason[vLang][outInput[i]] = outReason[i] = (r[i]);
                        livespell.cache.spell[vLang][outInput[i]] = outSpellingOk[i] = (t[i] === "T");
                        if (doSuggest && !livespell.cache.spell[vLang][outInput[i]]) {
                            var newSuggestions = Suggestions[i].split(livespell.str.chr(2));
                            livespell.cache.suggestions[vLang][outInput[i]] = outSuggestions[i] = newSuggestions;
                            for (var j = 0; j < newSuggestions.length; j++) {
                                livespell.cache.spell[vLang][newSuggestions[j]] = true;
                                var sug_each_word = newSuggestions[j].replace(/\-/g, " ").split(" ")
                                for (var k = 0; k < sug_each_word.length; k++) {
                                    livespell.cache.spell[vLang][sug_each_word[k]] = true;
                                }
                            }
                        }
                    }
                    if (outInput.length > 1 || command === "APISPELLARRAY") {
                        oSender.onSpellCheck(outInput, outSpellingOk, outReason, outSuggestions);
                    } else {
                        oSender.onSpellCheck(outInput[0], outSpellingOk[0], outReason[0], outSuggestions[0]);
                    }
                }
            }
        }, cache: {
            ignore: [],
            spell: [],
            reason: [],
            wordlist: [],
            suglist: [],
            langs: [],
            suggestions: [],
            suggestionrequest: null,
            checkTimeout: null,
            ajaxrequest: []
        }, test: {
            isword: function (str) {
                return (/^([\w'`´\x81-\xFF]+)$$/i).test(str);
            }, ALLCAPS: function (str) {
                return str === str.toUpperCase();
            }, eos: function (str) {
                return (/[!?¿¡.][\s]*$$/).test(str) && !((/[.]{3}/).test(str));
            },
            browserWebKit: function () {
                var webkit = /webkit/.test(navigator.userAgent.toLowerCase()) && /opera/.test(navigator.userAgent.toLowerCase())
			return webkit;
            }
		,
            num: function (str) {
                return (/[.0-9\*\#\@\/\%\$\&\+\=]/).test(str);
            }, lcFirst: function (str) {
                var f = str.substr(0, 1);
                return f == f.toLowerCase();
            }, spelling: function (word, Lang) {
                if (livespell.cache.ignore[word.toLowerCase()] && livespell.cache.ignore[word.toLowerCase()] === true) {
                    return true
                };
                if (!livespell.cache.spell[Lang]) {
                    livespell.cache.spell[Lang] = []
                }
                var res = (livespell.cache.spell[Lang][word]);
                if (res && typeof (res) == "function") { res = true };
                return res;

            }, fullyCached: function (word, lang, makeSuggestions) {
                var wordSpellCheck = this.spelling(word, lang);
                var result = wordSpellCheck === true || wordSpellCheck === false
                if (wordSpellCheck !== true) {
                    result = result && livespell.cache.reason[lang] && livespell.cache.reason[lang][word];
                    if (makeSuggestions) {
                        result = result && livespell.cache.suggestions[lang] && livespell.cache.suggestions[lang][word];
                    }
                }
                return result;
            }, browserValid: function () {
                return (document.designMode || document.contentEditable) && !(document.opera);
            }
        }, str: {
            getCase: function (word) {
                if (word.toUpperCase() === word) {
                    return 2;
                }
                if (livespell.str.toCaps(word) === word) {

                    return 1;
                }
                return 0;
            }, stripSpans: function (strinput) {
                if (!strinput) {
                    return ""
                }
                strinput = strinput.replace((/(\<\/span[^>]*\>)/gi), "");
                return strinput.replace((/(\<span[^>]*\>)/gi), "");
            },
            stripNonSpaceTags: function (strinput) {
                if (!strinput) {
                    return ""
                }
                strinput = strinput.replace((/(\<\!xml[^>]*\>)/gi), "");
                strinput = strinput.replace((/(\<span[^>]*\>)/gi), "");
                strinput = strinput.replace((/(\<\/span[^>]*\>)/gi), "");
                strinput = strinput.replace((/(\<\/div[^>]*\>)/gi), "");
                strinput = strinput.replace((/(\<div[^>]*\>)/gi), "");
                return strinput;
            },

            stripTags: function (strinput) {
                if (!strinput) {
                    return ""
                }
                return strinput.replace((/(<[\/]?[a-z][^>]*>)/gi), "");
            }, HTMLEnc: function (s) {
                s = (s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'));
                s = s.replace(/\n/g, "<br />");
                s = s.replace(/[ ][ ]/gi, " &nbsp;");
                s = s.replace(/[ ][ ]/gi, " &nbsp;");
                return s;
            }, HTMLDec: function (s) {
                s = this.encodeWhiteSpace(s);
                s = (s.replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>'));
                return s;
            }, HTMLDecAndStripFormatting: function (s) {
                s = s.replace((/<br[ ]*[\/]?>/gi), "\n");
                s = this.stripTags(s);
                s = s.replace(/\&nbsp;/gi, " ");
                s = (s.replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>'));
                return s;
            }, spliceXHTML: function (str, pos, add) {
                var arrStr = str.split('');
                var inHTML = false;
                var out = "";
                var j = 0;
                for (var i = 0; i < arrStr.length; i++) {
                    var char = arrStr[i];
                    if (char == "<") {
                        inHTML = true
                    }
                    out += char;
                    if (j == pos && !inHTML) {
                        out += add
                    }
                    if (!inHTML) {
                        j++;
                    }
                    if (char == ">" && inHTML) {
                        inHTML = false;
                    }
                }
                return out;
            }, spliceSpans: function (str, pos, add) {
                var arrStr = str.split('');
                var inHTML = false;
                var out = "";
                var j = 0;
                for (var i = 0; i < arrStr.length; i++) {
                    var char = arrStr[i];
                    try {

                        if (i < arrStr.length - 5 && char == "<" && ((arrStr[i + 1].toLowerCase() == "s" && arrStr[i + 2].toLowerCase() == "p" && arrStr[i + 3].toLowerCase() == "a" && arrStr[i + 4].toLowerCase() == "n") || (arrStr[i + 1].toLowerCase() == "/" && arrStr[i + 2].toLowerCase() == "s" && arrStr[i + 3].toLowerCase() == "p" && arrStr[i + 4].toLowerCase() == "a" && arrStr[i + 5].toLowerCase() == "n"))) {
                            inHTML = true
                        }
                    } catch (e) { }
                    out += char;
                    if (j == pos && !inHTML) {
                        out += add
                    }
                    if (!inHTML) {
                        j++;
                    }
                    if (char == ">" && inHTML) {
                        inHTML = false;
                    }
                }
                return out;
            },


            toCase: function ($$str, $$C, $$bcapitalize) {
                switch ($$C) {
                    case 2:
                        $$str = $$str.toUpperCase();
                        break;
                    case 1:
                        $$str = $$str.substr(0, 1).toUpperCase() + $$str.substr(1);
                        break;
                }
                if ($$bcapitalize) {
                    $$str = $$str.substr(0, 1).toUpperCase() + $$str.substr(1);
                }
                return $$str;
            }, tokenize: function (strdoc) {


                var pattern = (/((\&lt\;[\/\?]?[a-zA-Z][^\&]*\&gt;)|(\<[\/\?]?[a-z][^\>]*\>)|(\&lt\;[\/\?]?[a-z][.]*\&gt;)|(\&amp\;[a-zA-Z0-9]{1,6}\;)|(\&[a-zA-Z0-9]{1,6}\;)|([a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4})|(<[\/\?]?\w+[^>]*>)|([a-zA-Z]{2,5}:\/\/[^\s]*)|(www\.[^\s]+[\.][a-zA-Z-]{2,4})|([^\s\<\>]+[\.][a-zA-Z-]{2,4})|([\w'`¥íë\x81-\xFF]*[\w`¥íë\x81-\xFF])|([\w]+))/gi);


                var arrdocobj = strdoc.replace(pattern, this.chr(1) + "$1" + this.chr(1)).replace(/\x01\x01/g, this.chr(1)).split(this.chr(1));
                var arrdoc = [];
                for (var i = 0; i < arrdocobj.length; i++) {
                    arrdoc[i] = arrdocobj[i];
                }
                if (arrdoc[0] === "") {
                    arrdoc.shift();
                }
                if (arrdoc[arrdoc.length - 1] === "") {
                    arrdoc.pop();
                }


                return arrdoc;
            }, chr: function (AsciiNum) {
                return String.fromCharCode(AsciiNum);
            }, toCaps: function (str) {
                return str.substr(0, 1).toUpperCase() + str.substr(1);
            }, rtrim: function (s) {
                return s.replace(/\s*$$/, "");
            }, base64decode: function (input) {
                var _keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
                var output = "";
                var chr1, chr2, chr3;
                var enc1, enc2, enc3, enc4;
                var i = 0;
                input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
                while (i < input.length) {
                    enc1 = _keyStr.indexOf(input.charAt(i++));
                    enc2 = _keyStr.indexOf(input.charAt(i++));
                    enc3 = _keyStr.indexOf(input.charAt(i++));
                    enc4 = _keyStr.indexOf(input.charAt(i++));
                    chr1 = (enc1 << 2) | (enc2 >> 4);
                    chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
                    chr3 = ((enc3 & 3) << 6) | enc4;
                    output = output + String.fromCharCode(chr1);
                    if (enc3 != 64) {
                        output = output + String.fromCharCode(chr2);
                    }
                    if (enc4 != 64) {
                        output = output + String.fromCharCode(chr3);
                    }
                }
                output = this.utf8_decode(output);
                return output;
            }, utf8_decode: function (utftext) {
                var string = "";
                var i = 0;
                var c = c1 = c2 = 0;
                while (i < utftext.length) {
                    c = utftext.charCodeAt(i);
                    if (c < 128) {
                        string += String.fromCharCode(c);
                        i++;
                    }
                    else if ((c > 191) && (c < 224)) {
                        c2 = utftext.charCodeAt(i + 1);
                        string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                        i += 2;
                    }
                    else {
                        c2 = utftext.charCodeAt(i + 1);
                        c3 = utftext.charCodeAt(i + 2);
                        string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                        i += 3;
                    }
                }
                return string;
            }
        },
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        userDict: {
            load: function () {
                var current_cookie = livespell.cookie.get("SPELL_DICT_USER");
                if (!current_cookie.length) {
                    return;
                }
                var arrPersonalWords = current_cookie.split(livespell.str.chr(1));
                for (var i = 0; i < arrPersonalWords.length; i++) {
                    livespell.cache.ignore[arrPersonalWords[i].toLowerCase()] = true;
                }
            }, add: function (word) {
                livespell.cache.ignore[word.toLowerCase()] = true;
                var current_cookie = livespell.cookie.get("SPELL_DICT_USER");
                if (current_cookie) {
                    current_cookie = livespell.str.chr(1) + current_cookie;
                }
                current_cookie = word + current_cookie;
                livespell.cookie.setLocal("SPELL_DICT_USER", current_cookie);
            }
        },
        //////////////////////////////////////////////////////////////////////////////////////////////////////////
        cookie: {
            erase: function (name, path, domain) {
                if (livespell.cookie.get(name)) {
                    document.cookie = name + "=" + ((path) ? ";path=" + path : "") + ((domain) ? ";domain=" + domain : "") + ";expires=Thu, 01-Jan-1970 00:00:01 GMT";
                }
            }, get: function (check_name) {
                var a_all_cookies = document.cookie.split(';');
                var a_temp_cookie = '';
                var cookie_name = '';
                var cookie_value = '';
                var b_cookie_found = false;
                for (var i = 0; i < a_all_cookies.length; i++) {
                    a_temp_cookie = a_all_cookies[i].split('=');
                    cookie_name = a_temp_cookie[0].replace(/^\s+|\s+$$/g, '');
                    if (cookie_name === check_name) {
                        b_cookie_found = true;
                        if (a_temp_cookie.length > 1) {
                            cookie_value = unescape(a_temp_cookie[1].replace(/^\s+|\s+$$/g, ''));
                        }
                        if (!cookie_value) {
                            return "";
                        }
                        return cookie_value;
                    }
                    a_temp_cookie = null;
                    cookie_name = '';
                }
                if (!b_cookie_found) {
                    return "";
                }
            }, set: function (name, value, expires, path, domain, secure) {
                var today = new Date();
                today.setTime(today.getTime());
                if (expires) {
                    expires = expires * 1000 * 60 * 60 * 24;
                }
                var expires_date = new Date(today.getTime() + (expires));
                document.cookie = name + "=" + escape(value) + ((expires) ? ";expires=" + expires_date.toGMTString() : "") + ((path) ? ";path=" + path : "") + ((domain) ? ";domain=" + domain : "") + ((secure) ? ";secure" : "");
            }, setLocal: function (name, value) {
                var $$apage = (document.URL.toString().split(document.domain)[1].split("#")[0].split("?")[0].split("/"));
                $$apage[$$apage.length - 1] = "";
                if ($$apage[0].indexOf(":") > -1) {
                    $$apage[0] = "";
                }
                var $$page = $$apage.join("/");
                this.set(name, value, 999, $$page, "", false);
            }
        },
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        events: {
            add: function (obj, event, callback, capture) {
                if (obj.addEventListener) {
                    try {
                        obj.addEventListener(event, callback, false);
                    } catch (e) { }
                } else if (obj.attachEvent) {
                    obj.detachEvent("on" + event, callback);
                    obj.attachEvent("on" + event, callback);
                }
            }
        }
    };
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

    if (!Array.remove) {
        Array.prototype.remove = function (subject) {
            var r = new Array();
            for (var i = 0, n = this.length; i < n; i++) {
                if (!(this[i] == subject)) {
                    r[r.length] = this[i];
                }
            }
            return r;
        }
    }

    $$ = function (id) {
        return document.getElementById(id);
    }

    ////
    ///
    ///


    livespell.insitu = {
        settings: {
            Delay: 888
        },

        provider: function (id) {
            return livespell.spellingProviders[id];
        },

        initiated: false,
        _FIELDSUFFIX: "___livespell_proxy",
        _CONTEXTMENU: "livespell___contextmenu",
        updateBase: function (id) {

            $$(id).value = livespell.str.HTMLDecAndStripFormatting(livespell.insitu.getProxy(id));
        },
        ///////////////////////////////////////////////////////////////////////////////
        proxyDOM: function (id) {
            return $$(id + this._FIELDSUFFIX);
        },
        tabber: "<em>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</em>"
		,
        initer: "<span style='width:1px'></span>"
			,
        getProxy: function (id) {
            var doc = livespell.insitu.proxyDOM(id).innerHTML;
            if (document.body.currentStyle) {
                while (doc.indexOf(this.tabber) > -1) {
                    doc = doc.replace(this.tabber, "\t")
                }
                while (doc.indexOf(this.initer) > -1) {
                    doc = doc.replace(this.initer, "")
                }
            }

            doc = livespell.str.stripNonSpaceTags(doc)
            return doc;
        },
        ///////////////////////////////////////////////////////////////////////////////
        setProxy: function (id, val) {

            if (document.body.currentStyle) {
                val = val.replace(/\t/gi, this.tabber)
                val = val.replace(/\x0A/g, "<br>");
                val = val.replace(/\x0E/g, "");
            }

            if (val === "") { val = this.initer }


            if (livespell.insitu.proxyDOM(id).innerHTML === val) {
                return
            }


            livespell.insitu.proxyDOM(id).innerHTML = val;
            if (!(window.getComputedStyle)) {
                livespell.insitu.proxyDOM(id).innerHTML = livespell.insitu.proxyDOM(id).innerHTML;
                // IE Rendering hack
            }
        },

        updateProxy: function (id) {
            this.setProxy(id, $$(id).value);
        },


        setProxyAndMaintainCaret: function (id, val) {

            if (livespell.insitu.proxyDOM(id).innerHTML === val) { return }
            var dmilit = '\uFEFF';
            liveField = livespell.insitu.proxyDOM(id);
            if (window.getSelection) {

                range = window.getSelection().getRangeAt(0);

                range.insertNode(tNode = document.createTextNode(dmilit));


            } else {

                range = document.selection.createRange();
                range.pasteHTML(dmilit);
            }

            text = liveField.innerHTML;
            text = livespell.str.stripSpans(text);
            pos = text.indexOf(dmilit) - 1;


            caretNode = document.getElementById("livespell_cursor_hack__" + id)
            if (caretNode && caretNode.parentNode) {
                caretNode.parentNode.removeChild(caretNode);
            }

            caretNode = document.createElement("span");
            caretNode.id = "livespell_cursor_hack__" + id;
            plainNode = document.createElement("span");
            plainNode.appendChild(caretNode);
            caretNodeHTML = plainNode.innerHTML;
            plainNode.removeChild(caretNode);
            delete plainNode;
            delete caretNode;

            if (window.getSelection) {
                window.getSelection().removeAllRanges();
            }

            text = livespell.str.spliceSpans(val, pos, caretNodeHTML);

            liveField.innerHTML = text;
            if (!(window.getComputedStyle)) {
                liveField.innerHTML = liveField.innerHTML;
                // IE Rendering hack
            }


            if (window.getSelection) {
                selection = window.getSelection();
                selection.removeAllRanges();
                range = document.createRange();
                caretNode = document.getElementById("livespell_cursor_hack__" + id)
                if (caretNode) {
                    range.selectNode(caretNode);
                    selection.addRange(range);
                    range.collapse(false);

                    liveField.focus();


                    caretNode.parentNode.removeChild(caretNode);
                }

            } else {

                range = document.selection.createRange();
                caretNode = document.getElementById("livespell_cursor_hack__" + id)
                try {
                    range.moveToElementText(caretNode);
                    range.select();
                } catch (e) { }

                try { caretNode.parentNode.removeChild(caretNode); } catch (e) { }
            }

        },

        ///////////////////////////////////////////////////////////////////////////////
        checkNow: function (fieldList, providerID) {


            if (!livespell.cache.spell[this.provider(providerID).Language]) {
                livespell.cache.spell[this.provider(providerID).Language] = [];
                livespell.cache.reason[this.provider(providerID).Language] = [];
            }

            livespell.cache.wordlist[providerID] = [];

            if (!fieldList.join) { fieldList = fieldList.split(",") }

            var mem_words = [];
            for (var f = 0; f < fieldList.length; f++) {
                var id = fieldList[f];
                if ($$(id).nodeName.toLowerCase() == "textarea") {
                    livespell.insitu.createProxy(id);
                    var tokens_isword = [];
                    var strDoc = this.getProxy(id);
                    var tokens = livespell.str.tokenize(strDoc);
                    var lng = this.provider(providerID).Language
                    for (var i = 0; i < tokens.length; i++) {
                        if (mem_words["_" + tokens[i]] !== true && livespell.test.isword(tokens[i]) === true) {
                            var cachelookup = livespell.cache.spell[lng][tokens[i]];
                            if (cachelookup !== true && cachelookup !== false) {
                                mem_words["_" + tokens[i]] = true;
                                livespell.cache.wordlist[providerID].safepush(tokens[i]);
                            }
                        }
                    }
                }
            }

            if (!livespell.cache.wordlist[providerID].length) {
                return this.renderProxy(id, providerID);
            }
            delete mem_words;
            livespell.ajax.send("CTXSPELL", livespell.cache.wordlist[providerID].join(livespell.str.chr(1)), this.provider(providerID).Language, "", providerID);

        },
        ///////////////////////////////////////////////////////////////////////////////
        resetProxy: function (id) {
            this.createProxy(id);
            var n = document.getElementById(id + this._FIELDSUFFIX);
            if (!n) { return };

            livespell.events.add(n, "mouseup", function () { livespell.insitu.updateBase(id); }, false);
            livespell.events.add(n, "keyup", function () { livespell.insitu.updateBase(id); }, false);
            livespell.events.add(n, "keydown", livespell.insitu.keyhandler, false);
            livespell.events.add(n, "keypress", livespell.insitu.keypresshandler, true);
            livespell.events.add(n, "paste", livespell.insitu.pastehandler, false);

            var t = document.getElementById(id);
            t.focus = function () {
                try { n.focus(); } catch (e) { }
            }

        },
        destroyProxy: function (id) {



            if (!document.getElementById(id + this._FIELDSUFFIX)) { return }
            o = livespell.insitu.proxyDOM(id);
            n = document.getElementById(id);
            o.parentNode.removeChild(o);



            n.style.display = livespell.inlineblock();

            n.style.visibility = "visible";
            livespell.liveProxys.remove(id);

            n.hasLiveSpellProxy = false;

        },
        createProxy: function (id) {



            if (!livespell.test.browserValid()) { return }

            var e = document.getElementById(id);
            if (!e) { return }


            if (e.nodeName.toLowerCase() !== "textarea") { return };


            livespell.insitu.init();
            if (document.getElementById(id + this._FIELDSUFFIX)) { return livespell.insitu.proxyDOM(id); };


            var attr, stylesToCopy, i, styleval;
            var n = document.createElement("div");
            n.style.display = "none";
            n.setAttribute("id", id + this._FIELDSUFFIX);

            var t = $$(id);


            try {
                n.setAttribute("class", "livespell_textarea " + t.className);
            } catch (e) { }

            try {
                n.setAttribute("style", t.getAttribute("style"));
            } catch (e) { }
            n.style.display = "none";

            stylesToCopy = ["font-size", "line-height", "font-family", "width", "height", "padding-left", "padding-top", "margin-left", "margin-top", "padding-right", "padding-bottom", "margin-right", "margin-bottom", "font-weight", "font-style", "color", "text-transform", "text-decoration", "line-height", "text-align", "vertical-align", "direction", "background-color", "background-image", "background-repeat", "background-position", "background-attachment"]; //
            stylesToSet = ["fontSize", "lineHeight", "fontFamily", "width", "height", "paddingLeft", "paddingTop", "marginLeft", "marginTop", "paddingRight", "paddingBottom", "marginRight", "marginBottom", "fontWeight", "fontStyle", "color", "textTransform", "textDecoration", "lineHeight", "textAlign", "verticalAlign", "direction", "backgroundColor", "backgroundImage", "backgroundRepeat", "backgroundPosition", "backgroundAttachment"]; //


            if (window.getComputedStyle) {
                var compStyle = window.getComputedStyle(t, null);
                for (i = 0; i < stylesToCopy.length; i++) {
                    attr = stylesToCopy[i];
                    attr2 = stylesToSet[i];

                    styleval = compStyle.getPropertyValue(attr);

                    if (attr == "height" && styleval.indexOf("px")) {
                        styleval = (Number(styleval.split("px")[0]) + 1) + "px"; // FF wiggle hack
                    }
                    if (attr == "width" && styleval.indexOf("px")) {
                        styleval = (Number(styleval.split("px")[0]) - 1) + "px"; // FF wiggle hack
                    }

                    if (attr == "width") {
                        if (t.attributes["width"] && t.attributes["width"].value.indexOf('%') > -1) {
                            styleval = t.attributes["width"].value;
                        }
                        if (t.style.width && t.style.width.indexOf('%') > -1) {
                            styleval = t.style.width;
                        }
                    }

                    if (attr == "height") {
                        if (t.attributes["height"] && t.attributes["height"].value.indexOf('%') > -1) {
                            styleval = t.attributes["height"].value;
                        }
                        if (t.style.height && t.style.height.indexOf('%') > -1) {
                            styleval = t.style.height;
                        }
                    }




                    if (attr == "margin-left" && styleval.indexOf("px")) {
                        styleval = (Number(styleval.split("px")[0]) + 1) + "px"; // FF wiggle hack
                    }


                    if (styleval) {



                        n.style[attr2] = styleval;



                    }


                }




                ////////////////////////////////////////////////////////////////////////////////////////
            } else if (t.currentStyle) {

                n.style.overflowY = "scroll";
                for (i = 0; i < stylesToCopy.length; i++) {
                    var attr = stylesToSet[i];
                    var styleval = t.currentStyle[attr];

                    if (styleval) {

                        try {

                            if (attr == "width") {
                                try {
                                    if (t.offsetWidth) {
                                        n.style.width = t.offsetWidth;
                                    }
                                } catch (e) { }

                                if (t.attributes["width"] && t.attributes["width"].value.indexOf('%') > -1) {
                                    styleval = t.attributes["width"].value;
                                }
                                if (t.style.width && t.style.width.indexOf('%') > -1) {
                                    styleval = t.style.width;
                                }
                            }

                            if (attr == "height") {
                                try {
                                    if (t.offsetHeight) {
                                        n.style.height = t.offsetHeight;
                                    }
                                } catch (e) { }

                                if (t.attributes["height"] && t.attributes["height"].value.indexOf('%') > -1) {
                                    styleval = t.attributes["height"].value;
                                }
                                if (t.style.height && t.style.height.indexOf('%') > -1) {
                                    styleval = t.style.height;
                                }
                            }

                            styleval = styleval + "";

                            if (!styleval.toUpperCase) {


                            } else if (styleval.toUpperCase() != "AUTO" && styleval.toUpperCase() != "INHERIT") {

                                n.style[attr] = styleval;
                            }

                        } catch (e) { }
                    }
                }

                stylesToCopy = ["font-size", "line-height", "font-family", "font-weight", "font-style", "color", "text-transform", "text-decoration", "line-height", "text-align", "vertical-align", "direction"]; //
                stylesToSet = ["fontSize", "lineHeight", "fontFamily", "fontWeight", "fontStyle", "color", "textTransform", "textDecoration", "lineHeight", "textAlign", "verticalAlign", "direction"]; //


                mycss = "";
                csstext = "#" + n.id + " p   , #+" + n.id + " span {";
                for (i = 0; i < stylesToCopy.length; i++) {
                    try {
                        csstext += stylesToCopy[i] + " : " + t.currentStyle[stylesToSet[i]] + "; ";
                    } catch (e) { }



                }

                csstext += "margin:  0; ";
                csstext += "padding: 0; ";
                csstext += "border: 0; ";

                csstext += "} ";

                this.addCss(csstext);



            }

            n.isLiveSpellProxy = true;
            n.className = "livespell_textarea";
            n.setAttribute("hasFocus", false)
            n.style.display = livespell.inlineblock()
            t.style.display = "none";
            t.style.visibility = "hidden";

            t.hasLiveSpellProxy = true;
            n.contentEditable = 'true';
            n.designMode = 'on';


if(!livespell.test.browserWebKit()){
            /// Textarea Mimic End 
            try {
                document.body.setAttribute = ("spellCheck", "false");
            } catch (e) { }
            try {
                document.body.spellcheck = false
            } catch (e) { }
}
            livespell.events.add(n, "mouseup", function () { livespell.insitu.updateBase(id); }, false);
            livespell.events.add(n, "keyup", function () { livespell.insitu.updateBase(id); }, false);
            livespell.events.add(n, "keypress", livespell.insitu.keypresshandler, true);
            livespell.events.add(n, "keydown", livespell.insitu.keyhandler, false);
            livespell.events.add(n, "paste", livespell.insitu.pastehandler, false);


            t.focus = function () {
                try { n.focus(); } catch (e) { }
            }


            var trueparent = t.parentNode

            var truesib = t;

            if (t.currentStyle) {
                while (trueparent.nodeName == "P" || trueparent.nodeName == "H1" || trueparent.nodeName == "H2" || trueparent.nodeName == "H3" || trueparent.nodeName == "H4" || trueparent.nodeName == "H5" || trueparent.nodeName == "H6") {
                    truesib = trueparent;
                    trueparent = trueparent.parentNode

                }
            }

            if (trueparent.hasChildNodes) {
                trueparent.insertBefore(n, truesib)
            } else {
                trueparent.appendChild(n)
            }



            var o = livespell.insitu.proxyDOM(id);

            o.hasFocus = false;
            livespell.events.add(o, "focus", function () {
                o.hasFocus = true
            }, false);
            livespell.events.add(o, "blur", function () {
                o.hasFocus = false
            }, false);



            livespell.liveProxys.safepush(id);


            this.setProxy(id, livespell.str.HTMLEnc($$(id).value));



            return o;
        },

        addCss: function (cssCode) {

            var styleElement = document.createElement("style");
            styleElement.type = "text/css";
            if (styleElement.styleSheet) {
                styleElement.styleSheet.cssText = cssCode;
            } else {
                styleElement.appendChild(document.createTextNode(cssCode));
            }
            document.getElementsByTagName("head")[0].appendChild(styleElement);
        }

		,
        ///////////////////////////////////////////////////////////////////////////////
        renderProxy: function (fieldList, providerID) {
            if (!fieldList) { return }
            if (!fieldList.join) { fieldList = fieldList.split(",") }

            for (var j = 0; j < fieldList.length; j++) {
                var id = fieldList[j];
                if (!livespell.insitu.proxyDOM(id)) {
                } else {

                    var strDoc = this.getProxy(id);



                    var token;
                    var tokens = livespell.str.tokenize(strDoc);
                    var tokens_startsentence = [];
                    var tokens_isword = [];
                    var show_error;
                    for (var i = 0; i < tokens.length; i++) {
                        token = tokens[i];
                        tokens_isword[i] = livespell.test.isword(token);
                        tokens_startsentence[i] = (tokens_isword && (i === 0 || (livespell.test.eos(tokens[i - 1]))));
                        show_error = false;
                        var reason = livespell.cache.reason[this.provider(providerID).Language][token] ? livespell.cache.reason[this.provider(providerID).Language][token] : "";
                        if (tokens_isword[i]) {
                            if (typeof (livespell.test.spelling(token, this.provider(providerID).Language)) == "undefined") {
                                var fxs = this.provider(providerID);
                                setTimeout(function () { fxs.CheckInSitu() }, 1 + Math.round(Math.random() * 10));
                                return;
                            }


                            if (livespell.test.spelling(token, this.provider(providerID).Language) != true) {
                                show_error = true;
                            }
                            if (show_error) {
                                if (this.provider(providerID).IgnoreAllCaps && token === token.toUpperCase() && reason !== "B" && reason !== "E") {
                                    show_error = false;
                                }
                                if (this.provider(providerID).IgnoreNumeric && (livespell.test.num(token)) && reason !== "B" && reason !== "E") {
                                    show_error = false;
                                }
                                if (!this.provider(providerID).CaseSensitive && reason == "C") {
                                    show_error = false;
                                }
                            }
                            if (!tokens_startsentence[i] && i > 1 && token.toUpperCase() === tokens[i - 2].toUpperCase()) {

                                show_error = true;
                                reason = "R";
                            }
                            if (this.provider(providerID).CaseSensitive && this.provider(providerID).CheckGrammar && tokens_startsentence[i] && livespell.test.lcFirst(token)) {
                                if (strDoc.indexOf(".") > 0 || strDoc.indexOf("!") > 0 || strDoc.indexOf("?") > 0 || strDoc.length > 50) {
                                    show_error = true;
                                    reason = "G";
                                }
                            }

                        }
                        if (show_error) {
                            var wiggleClass = "livespell_redwiggle";
                            if (reason === "R" || reason === "G") {
                                wiggleClass = "livespell_greenwiggle";
                            }
                            tokens[i] = "<span class='" + wiggleClass + "'   onmousedown='return livespell.insitu.disableclick(event);' onmouseup='return livespell.insitu.typoclick(event,this,\"" + reason + "\",\"" + providerID + "\")' >" + (token) + "</span>";

                        } else {

                            tokens[i] = (token);

                        }

                    }

                    text = tokens.join('');
                    if (livespell.insitu.proxyDOM(id).hasFocus) {
                        this.setProxyAndMaintainCaret(id, text);
                    } else {
                        this.setProxy(id, text)
                    }





                }
            }

        },
        ///////////////////////////////////////////////////////////////////////////////
        init: function () {
            if (livespell.insitu.initiated) return;
            livespell.insitu.initiated = true;
            livespell.context.renderShell()
            livespell.context.renderCss();
            livespell.events.add(window.document, "mousedown", livespell.context.hideIfNotinUse, false);
            livespell.events.add(window.document, "keydown", livespell.context.hide, false);
            livespell.userDict.load();
            livespell.context.hide();
        },
        allTextAreas: function () {
            var AF = [];
            oTAreas = document.getElementsByTagName("textarea")
            for (var i = 0; i < oTAreas.length; i++) {
                var Area = oTAreas[i];

                var ok = true;

                if (Area.style.display == "none") { ok = false; }
                if (Area.style.visibility == "hidden") { ok = false; }
                if (!ok && Area.hasLiveSpellProxy) { ok = true; }
                if (Area.disabled) { ok = false; }
                if (ok) {
                    if (!Area.id) {
                        Area.id = "livespell__textarea__" + i
                    }
                    AF.push(Area.id);
                }
            }
            return AF
        },
        allTextInputs: function () {
            var AF = [];
            oTInputs = document.getElementsByTagName("input")
            for (var i = 0; i < oTInputs.length; i++) {
                var Area = oTInputs[i];
                if (Area.hasSpellProxy == true) { AF.push(Area.id) }
                else if (Area.type.toLowerCase() === "text" && !Area.disabled && Area.style.display !== "none" && Area.style.visibility !== "hidden") {
                    if (!Area.id) {
                        Area.id = "livespell__input__" + i
                    }
                    AF.push(Area.id); Area.hasSpellProxy = true
                }
            }
            return AF
        }
	,
        allEditors: function () {
            var AF = [];
            oIFrmaes = document.getElementsByTagName("iframe");
            for (var i = 0; i < oIFrmaes.length; i++) {

                var myFrame = oIFrmaes[i];
                oDoc = livespell.getIframeDocument(myFrame);

                if (oDoc) {
                    if (!myFrame.id) { myFrame.id = "livespell_rich_editor_id_" + i }
                    AF.push(myFrame.id);
                }

            }




            return AF;
        }
	,
        ///////////////////////////////////////////////////////////////////////////////
        pastehandler: function (e) {
            var me = e.srcElement ? e.srcElement : this;

            if (me.autocheck) {
                var base_field_id = me.id.split(livespell.insitu._FIELDSUFFIX)[0];
                var ProviderId = Number(me.autocheckProvider);

                setTimeout(function () {
                    livespell.insitu.checkNow(base_field_id, ProviderId)
                }, 10)
            }

        },

        keypresshandler: function (e) {


            try {
                if (!e) {
                    e = window.event;
                }
            } catch (e) { }

            e.cancelBubble = true;
            if (e.stopPropagation) {
                e.stopPropagation();
            }



        }
,

        keyhandler: function (event) {
            var returnfalse = false;
            try {
                if (!event) {
                    event = window.event;
                }
            } catch (e) { }

            var char = event.keyCode;

            /* For Text Input
            if(char==13){
            e = event;
            //e.cancelBubble is supported by IE - this will kill the bubbling process.
            e.cancelBubble = true;
            e.returnValue = false;

            //e.stopPropagation works only in Firefox.
            if (e.stopPropagation) {
            e.stopPropagation();
            e.preventDefault();
            }
            return false
            }
		
            */



            if (((char > 16 && char < 32) || (char > 32 && char < 41)) && (char != 127)) {
                return;
            }


            livespell.insitu.ignoreAtCursor();
            var me = event.srcElement ? event.srcElement : this;



            me.hasFocus = true;
            //////

            if (me.autocheck) {
                var base_field_id = me.id.split(livespell.insitu._FIELDSUFFIX)[0];
                var ProviderId = Number(me.autocheckProvider);
                var oProvider = livespell.spellingProviders[ProviderId];
                clearTimeout(livespell.cache.checkTimeout)
                livespell.cache.checkTimeout = setTimeout(function () {
                    livespell.insitu.checkNow(base_field_id, ProviderId)
                }, oProvider.Delay)
            }
            ////	

            if (returnfalse) { return false };
        },
        ignoreAtCursor: function () {

            var target
            try {
                if (window.getSelection) {
                    target = window.getSelection().focusNode.parentNode;
                } else if (document.selection) {
                    target = document.selection.createRange().parentElement();

                }
            } catch (e) { }


            if (target && target.nodeName.toUpperCase() === "SPAN") {
                target.className = "";
                target.onmousedown = null;
            }
        }

	,
        disableclick: function (event) {
            if (event.preventDefault)
            { event.preventDefault(); }
            else
            { event.returnValue = false; }
            return false
        },
        ///////////////////////////////////////////////////////////////////////////////
        typoclick: function (event, ospan, reason, providerID) {
            if (event.preventDefault)
            { event.preventDefault(); }
            else
            { event.returnValue = false; }


            livespell.context.caller = ospan;
            var parent;
            var p_walker = ospan;
            while (!parent) {
                if (p_walker.nodeName.toUpperCase() === "DIV") {
                    parent = p_walker;
                } else {
                    p_walker = p_walker.parentNode;
                }
            }
            var id = parent.id.split(this._FIELDSUFFIX)[0];
            if (!id.length) {
                return false;
            }
            var token = livespell.str.stripTags(ospan.innerHTML);
            var posx = 0;
            var posy = 0;
            if (!event) {
                event = window.event;
            }
            if (event.pageX || event.pageY) {
                posx = event.pageX;
                posy = event.pageY;
            } else if (event.clientX || event.clientY) {
                posx = event.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
                posy = event.clientY + document.body.scrollTop + document.documentElement.scrollTop;
            }
            posx += 2;
            posy += 2;

            livespell.context.DOM().className = "livespell_contextmenu"
            livespell.context.DOM().style.position = "absolute";
            livespell.context.DOM().style.top = posy + "px";
            livespell.context.DOM().style.left = posx + "px";
            livespell.context.providerID = providerID;
            livespell.context.suggest(id, token, reason, ospan);
            return false;
        }
    }



    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




    livespell.context = {
        mouseoverme: false,
        caller: null,
        providerID: null,
        currentWord: function () {
            return this.caller.innerHTML;
        },
        provider: function () {
            return livespell.spellingProviders[this.providerID]
        },
        DOM: function () {
            return $$(livespell.insitu._CONTEXTMENU);
        },
        langInSelection: false
        ,
        ///////////////////////////////////////////////////////////////////////////////
        hideIfNotinUse: function () {
            if (!livespell.context.mouseoverme & !livespell.context.langInSelection) {
                livespell.context.hide();
            }
        },
        ///////////////////////////////////////////////////////////////////////////////
        hide: function () {

            if (livespell.context.DOM().style.display != "none") {

                livespell.context.DOM().style.display = "none"
            }


        },
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////
        change: function (word) {

            this.provider().onChangeWord(this.currentWord(), word);
            var b = this.base_field_id();
            this.caller.parentNode.insertBefore(document.createTextNode(word), this.caller);
            this.caller.parentNode.removeChild(this.caller);
            this.hide();
            livespell.insitu.updateBase(b);
        },
        ///////////////////////////////////////////////////////////////////////////////
        ignore: function () {

            this.provider().onIgnore(this.currentWord());
            var p = this.caller;
            var b = this.base_field_id();
            this.caller.parentNode.insertBefore(document.createTextNode(this.currentWord()), this.caller);
            this.caller.parentNode.removeChild(this.caller);
            this.hide();
        },
        ///////////////////////////////////////////////////////////////////////////////
        del: function () {
            var b = this.base_field_id();
            var p = this.caller;
            var pp = p.parentNode;
            pp.removeChild(p);
            this.hide();
            livespell.insitu.updateBase(b);
        },
        ///////////////////////////////////////////////////////////////////////////////
        ignoreAll: function () {
            this.provider().onIgnoreAll(this.currentWord());
            livespell.cache.ignore[this.currentWord().toLowerCase()] = true;
            livespell.insitu.renderProxy(this.base_field_id(), this.providerID);
            this.hide();
        },
        ///////////////////////////////////////////////////////////////////////////////
        addPersonal: function () {
            this.provider().onLearnWord(this.currentWord());
            word = this.currentWord().toLowerCase();
            livespell.userDict.add(word)
            livespell.insitu.renderProxy(this.base_field_id(), this.providerID);
            this.hide();
        },
        ///////////////////////////////////////////////////////////////////////////////
        changeLanguage: function (strLang) {
            if (this.provider().Language == strLang) { return }
            this.provider().Language = strLang;
            this.provider().onChangeLanguage(strLang);
            this.provider().CheckInSitu();

            this.hide();
        },
        ///////////////////////////////////////////////////////////////////////////////
        showMultiLang: function () {
            $$('livepell__multilanguage').style.display = "block";
            var livelangs = livespell.insitu.provider(this.providerID).Language.split(",");
            for (var i = 0; i < livespell.cache.langs.length; i++) {
                $$('livepell__multilanguage_' + livespell.cache.langs[i]).checked = false;
                for (var j = 0; j < livelangs.length; j++) {
                    if ((livespell.cache.langs[i] === livelangs[j])) {
                        $$('livepell__multilanguage_' + livespell.cache.langs[i]).checked = true;
                    }
                }
            }
        },
        ///////////////////////////////////////////////////////////////////////////////
        hideMultiLang: function () {
            $$('livepell__multilanguage').style.display = "none";
        },
        ///////////////////////////////////////////////////////////////////////////////
        base_field_id: function () {
            var parent = null;
            var p_walker = this.caller;
            while (!parent) {
                if (p_walker.nodeName.toUpperCase() === "DIV") {
                    parent = p_walker;
                } else {
                    p_walker = p_walker.parentNode;
                }
            }
            return parent.id.split(livespell.insitu._FIELDSUFFIX)[0];
        },
        ///////////////////////////////////////////////////////////////////////////////
        showMenu: function (id, word, reason, providerID) {
            this.providerID = providerID;
            this.DOM().style.display = "block";
            var j;
            var action = "REPLACE";
            var suggs = livespell.cache.suggestions[livespell.spellingProviders[providerID].Language][word];

            switch (reason) {
                case "B":
                    suggs = [];
                    suggs[0] = livespell.lang.fetch(providerID, "MENU_DELETEBANNED");
                    action = "DELETE";
                    break;
                case "R":
                    suggs = [];
                    suggs[0] = livespell.lang.fetch(providerID, "MENU_DELETEREPEATED");
                    action = "DELETE";
                    break;
                case "G":
                    if (!suggs.length) {
                        suggs[0] = word;
                    }
                    for (j = 0; j < suggs.length; j++) {
                        suggs[j] = livespell.str.toCaps(suggs[j]);
                    }
                    break;
                default:
                    //	case "S" , "C" , "E": 

                    var oCase = livespell.str.getCase(word);

                    if (oCase === 2) {
                        for (j = 0; j < suggs.length; j++) {
                            suggs[j] = suggs[j].toUpperCase();
                        }
                    } else if (oCase === 1) {
                        for (j = 0; j < suggs.length; j++) {

                            suggs[j] = livespell.str.toCaps(suggs[j]);
                        }
                    }
            }

            if (!suggs.length || suggs[0].length === 0) {

                suggs = [];
                suggs[0] = livespell.lang.fetch(providerID, "MENU_NOSUGGESTIONS");

                action = "IGNORE";
            }

            if (reason === "X") { action = "REG" }
            var dsuggs = [];
            for (j = 0; j < suggs.length; j++) {
                dsuggs.safepush(suggs[j]);
            }

            this.render(dsuggs, action, providerID);
        },
        ///////////////////////////////////////////////////////////////////////////////
        setMultiLang: function () {
            var langboxes = document.getElementById("livepell__multilanguage").getElementsByTagName("input");
            var checked = [];
            for (var i = 0; i < langboxes.length; i++) {
                var box = langboxes[i];
                if (box.checked) {
                    checked.push(box.value);
                }
            }
            if (!checked.length) {
                return;
            }
            this.provider().Language = (checked.join(","));
            this.provider().onChangeLanguage(this.provider().Language);
            livespell.insitu.checkNow(this.base_field_id(), this.providerID);
            this.hide();
        },
        ///////////////////////////////////////////////////////////////////////////////
        renderShell: function () {

            if ($$(livespell.context.DOM())) {
                return;
            }
            var n = document.createElement("div");
            n.setAttribute("id", livespell.insitu._CONTEXTMENU);
            document.body.appendChild(n);
            n.onmouseover = function () {
                livespell.context.mouseoverme = true;
            };
            n.onmouseout = function () {
                livespell.context.mouseoverme = false;
            };
        }
		,
        ///////////////////////////////////////////////////////////////////////////////
        renderCss: function (strTheme) {
            var idname = "__livespell__stylesheet";
            strTheme = strTheme ? strTheme : "classic"; // default
            var fileref = $$(idname)
            if (fileref) {
                fileref.setAttribute("href", livespell.installPath + "themes/" + strTheme + "/context-menu.css");
            } else {
                fileref = document.createElement("link");
                fileref.setAttribute("id", idname);
                fileref.id = idname;
                fileref.setAttribute("rel", "stylesheet");
                fileref.setAttribute("type", "text/css");
                fileref.setAttribute("href", livespell.installPath + "themes/" + strTheme + "/context-menu.css");
                document.getElementsByTagName("head")[0].appendChild(fileref);
            }
        },
        ///////////////////////////////////////////////////////////////////////////////

        buttonIsHidden: function (strId, providerID) {
            var oProvider = livespell.spellingProviders[providerID];
            var arrHideButtons = (oProvider.HiddenButtons.split(","))
            for (var i = 0; i < arrHideButtons.length; i++) {
                strBtn = arrHideButtons[i];
                if (strBtn.toLowerCase() === strId.toLowerCase()) { return true }
            }
            return false;

        },

        render: function (suggs, action, providerID) {



            var menuHTML = '';
            menuHTML += '<ul>';
            var i;
            for (var j = 0; j < suggs.length; j++) {
                switch (action) {
                    case "REPLACE":
                        menuHTML += '<li><a href="#"   onclick="livespell.context.change(this.innerHTML); return false" >' + suggs[j] + '</a></li>';
                        break;
                    case "IGNORE":

                        menuHTML += '<li><a href="#"   onclick="livespell.context.ignore(); ; return false" >' + suggs[j] + '</a></li>';

                        break;
                    case "REG":

                        suggs = [""];
                        if (livespell.spellingProviders[providerID].isUniPacked) {
                            menuHTML += '<li><a href="#"    onclick="window.open(\'http://www.javascriptspellcheck.com/Purchase\');return false;" >' + "JS SpellCheck Trial" + '</a></li>';
                        }
                        else if (livespell.spellingProviders[providerID].ServerModel.toLowerCase() == "asp.net") {
                            menuHTML += '<li><a href="#"   onclick="window.open(\'http://www.aspnetspell.com/Purchase\');return false;" >' + "ASPNetSpell Trial" + '</a></li>';
                        } else {
                            menuHTML += '<li><a href="#"   onclick="window.open(\'http://www.phpspellcheck.com/Purchase\');return false;" >' + "PHPSpellCheck Trial" + '</a></li>';
                        }
                        break;
                    case "DELETE":
                        menuHTML += '<li><a href="#"   onclick="livespell.context.del(); return false" >' + suggs[j] + '</a></li>';
                        break;
                }
            }
            menuHTML += '<li><hr /></li>';
            if (!this.buttonIsHidden("menuIgnore", providerID)) {
                menuHTML += '<li><a href="#" onclick="livespell.context.ignore(); return false">' + livespell.lang.fetch(providerID, "MENU_IGNORE") + '</a></li>';
            }
            if (!this.buttonIsHidden("menuIgnoreAll", providerID)) {
                menuHTML += '<li><a href="#"  onclick="livespell.context.ignoreAll(); return false">' + livespell.lang.fetch(providerID, "MENU_IGNOREALL") + '</a></li>';
            }
            if (!this.buttonIsHidden("menuAddToDict", providerID)) {
                menuHTML += '<li><a href="#"  onclick="livespell.context.addPersonal(); return false">' + livespell.lang.fetch(providerID, "MENU_LEARN") + '</a></li>';
            }

            menuHTML += '<li><hr /></li>';
            if (!livespell.MultipleDictionaries) {
                menuHTML += '<li>';
                //  
                if (/*@cc_on!@*/false) {
                    menuHTML += '<select  onfocus="livespell.context.langInSelection=true" onchange="livespell.context.langInSelection=false ;livespell.context.changeLanguage(this.options[this.selectedIndex].value)"  >';
                } else {
                    menuHTML += '<select  onchange="livespell.context.changeLanguage(this.options[this.selectedIndex].value)"  >';

                }
                for (i = 0; i < livespell.cache.langs.length; i++) {
                    var strselection = (livespell.cache.langs[i] === livespell.spellingProviders[providerID].Language) ? " selected = selected " : "";
                    menuHTML += '<option   ' + strselection + '  >' + livespell.cache.langs[i] + '</option>';
                }
                menuHTML += '</select>';

            } else {
                menuHTML += "<li><a href='javascript:livespell.context.showMultiLang()' >" + livespell.lang.fetch(providerID, "MENU_LANGUAGES") + "</a></li>";
                menuHTML += '<li id="livepell__multilanguage" style="display:none">';
                if (livespell.cache.langs.length > 5) {
                    menuHTML += '<div  class="livespell_contextmenu_multilang_container_scroll" >';
                } else {
                    menuHTML += '<div class="livespell_contextmenu_multilang_container_noscroll" >';
                }
                for (i = 0; i < livespell.cache.langs.length; i++) {
                    menuHTML += '<label>';
                    menuHTML += '<input type="checkbox" id="livepell__multilanguage_' + livespell.cache.langs[i] + '" value="' + livespell.cache.langs[i] + '" />';
                    menuHTML += livespell.cache.langs[i];
                    menuHTML += '</label>';
                    menuHTML += '<br/>';
                }
                menuHTML += '</div>';
                menuHTML += '<input type="button" value="' + livespell.lang.fetch(providerID, "MENU_CANCEL") + '" onclick="livespell.context.hideMultiLang()" /> ';
                menuHTML += '<input type="button" value="' + livespell.lang.fetch(providerID, "MENU_APPLY") + '" onclick="livespell.context.setMultiLang()" /> ';
                menuHTML += '</li>';
            }
            menuHTML += '</ul>';
            this.DOM().innerHTML = menuHTML;
            this.DOM().focus();

        },
        ///////////////////////////////////////////////////////////////////////////////
        suggest: function (id, word, reason, caller) {
            var Lang = [livespell.spellingProviders[this.providerID].Language]
            livespell.cache.suggestionrequest = {};
            if (livespell.cache.suggestions[Lang] && livespell.cache.suggestions[Lang][word]) {
                return livespell.context.showMenu(id, word, reason, this.providerID);
            }
            livespell.cache.suggestionrequest.id = id;
            livespell.cache.suggestionrequest.word = word;
            livespell.cache.suggestionrequest.reason = reason;
            livespell.cache.suggestionrequest.providerID = this.providerID;
            livespell.ajax.send("CTXSUGGEST", word, Lang, livespell.cache.langs.length ? "" : "ADDLANGS", this.providerID);
        }
    }
    function setup___livespell() {
        var tags = document.getElementsByTagName('script');
        var thisTag = tags[tags.length - 1];
        var path = thisTag.getAttribute("src").replace(/[\/]?include\.js/ig, "") + "/";

        if (path != "/") {
            livespell.installPath = path;

        } else {
            livespell.lang.load("en");
        }
        setInterval(livespell.heartbeat, 200)

        if (/*@cc_on!@*/false) {
            document.write('<script type="text/vbscript">\nsub document_oncontextmenu() on error resume next \ndim Oelement	  \n  Set   Oelement = window.event.srcElement	\n	 IF(   (Oelement.className="livespell_redwiggle" OR Oelement.className="livespell_greenwiggle")) THEN	\n	  window.event.returnValue = false 	 \n     window.event.cancelBubble = true\n	END IF \n	end sub\n	</script>');
        }

        function livespell___FF__clickmanager(e) {
            if (e.which && e.which == 3) {
                var t = (e.originalTarget);

                if (t && t.className && (t.className == "livespell_redwiggle" || t.className == "livespell_greenwiggle")) {
                    e.preventDefault();
                }
                var t = (e.target);

                if (t && t.className && (t.className == "livespell_redwiggle" || t.className == "livespell_greenwiggle")) {
                    e.preventDefault();
                }
            }
        }
        if (document.addEventListener && navigator && navigator.userAgent.toUpperCase().indexOf("WINDOWS") > 0) {
            if (document.addEventListener) {
                document.addEventListener('click', livespell___FF__clickmanager, true);
            }
        }

    }
    setup___livespell();
}

try{ if (Sys && Sys.Application) {
        Sys.Application.notifyScriptLoaded();}
} catch (e) { }