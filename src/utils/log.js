import i18n from "./i18n";

class WikiplusError extends Error {
    constructor(message, code) {
        super(message);
        this.code = code;
        this.name = "WikiplusError";
    }
}

class Log {
    static debug(message = "") {
        console.debug(`[Wikiplus-DEBUG] ${message}`);
    }
    static info(message = "") {
        console.info(`[Wikiplus-INFO] ${message}`);
    }
    static error(errorCode, payloads = []) {
        const template = i18n.translate(errorCode);
        let message = template;
        
        if (payloads.length > 0) {
            message = message.replace(/\$\{(\d+)\}/g, (_, index) => {
                const idx = parseInt(index, 10) - 1;
                return (idx >= 0 && idx < payloads.length) ? payloads[idx] : `$\{${index}\}`;
            });
        }
        console.error(`[Wikiplus-ERROR] ${message}`);
        
        throw new WikiplusError(message, errorCode);
    }
}

export default Log;