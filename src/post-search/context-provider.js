import { createContext, useContext, useState } from 'react';

const SettingsContext = createContext();

export const useSettingsContext = () => useContext(SettingsContext);

export const SettingProvider = ({ children }) => {
    const [sharedData, setSharedData] = useState({ key: 'value' });

    return (
        <SettingsContext.Provider value={{ sharedData, setSharedData }}>
            {children}
        </SettingsContext.Provider>
    );
};
