(() => {
    const init = () => {
        const data = window.JobSpreaderData || {};
        const { apiKey, scriptPlacement } = data;

        if (!apiKey) return;
        if (document.getElementById("jobspreader-js")) return;

        const url = new URL("https://jobspreader.com/pxl/script.min.js");
        url.search = new URLSearchParams({
            jsappid: apiKey,
            ts: String(Date.now()), // cache-buster
        }).toString();

        const script = document.createElement("script");
        script.id = "jobspreader-js";
        script.src = url.toString();
        script.async = true;

        const target =
            scriptPlacement === "body" ? document.body : document.head;

        (target || document.documentElement).appendChild(script);
    };

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init, { once: true });
    } else {
        init();
    }
})();
