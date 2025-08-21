function loadScript(src) {
    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = src;
        script.type = "text/javascript";
        script.defer = true; // 或 async
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    });
}

if (
    document.querySelector("[toast-list]") ||
    document.querySelector("[data-choices]") ||
    document.querySelector("[data-provider]")
) {
    loadScript("https://cdn.jsdelivr.net/npm/toastify-js");
    loadScript("assets/libs/choices.js/public/assets/scripts/choices.min.js");
    loadScript("assets/libs/flatpickr/flatpickr.min.js");
}
