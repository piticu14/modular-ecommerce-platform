import { IntlProvider } from "react-intl";
import { createContext, useState } from "react";

import type { ReactNode } from "react";

import cs from "../locales/cs.json";
import en from "../locales/en.json";

type Locale = "cs" | "en";

type LocaleContextType = {
    locale: Locale;
    setLocale: (locale: Locale) => void;
};

const messages: Record<Locale, typeof cs> = {
    cs,
    en
};

export const LocaleContext = createContext<LocaleContextType>({
    locale: "cs",
    setLocale: () => {}
});

export default function AppIntlProvider({ children }: { children: ReactNode }) {
    const getInitialLocale = (): Locale => {
        const stored = localStorage.getItem("locale");

        if (stored === "cs" || stored === "en") {
            return stored;
        }

        return "cs";
    };

    const [localeState, setLocaleState] = useState<Locale>(getInitialLocale);

    const setLocale = (locale: Locale) => {
        localStorage.setItem("locale", locale);
        setLocaleState(locale);
    };

    return (
        <LocaleContext.Provider value={{ locale: localeState, setLocale }}>
            <IntlProvider
                locale={localeState}
                messages={messages[localeState]}
            >
                {children}
            </IntlProvider>
        </LocaleContext.Provider>
    );
}