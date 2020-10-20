test('iframe_js.php', function (assert, document) {
    wait(assert, function () {
        return document.defaultView.iframeLoads > 0;
    }, function () {
        var done = assert.async();
        setTimeout(function () {
            done();
            assert.equal(document.defaultView.iframeLoads, 1, "The IFrame should have been loaded once");
            assert.ok(document.defaultView.srcWasChanged, "Initially, the IFrame src should have had an src of about:blank");
        }, 50);
    });
});
