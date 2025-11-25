import React, { createContext, useContext, useState } from 'react';

type SelectionsMap = Record<string, string[]>;

type BookingSelection = {
  selections: SelectionsMap;
  getForCategory: (category: string) => string[];
  setForCategory: (category: string, items: string[]) => void;
  addToCategory: (category: string, item: string) => void;
  removeFromCategory: (category: string, item: string) => void;
  clearCategory: (category: string) => void;
  clearAll: () => void;
};

const BookingSelectionContext = createContext<BookingSelection | undefined>(undefined);

export const BookingSelectionProvider = ({ children }: { children: React.ReactNode }) => {
  const [selections, setSelections] = useState<SelectionsMap>({});

  const getForCategory = (category: string) => selections[category] ?? [];

  const setForCategory = (category: string, items: string[]) =>
    setSelections((p) => ({ ...p, [category]: items }));

  const addToCategory = (category: string, item: string) =>
    setSelections((p) => {
      const prev = p[category] ?? [];
      if (prev.includes(item)) return p;
      return { ...p, [category]: [...prev, item] };
    });

  const removeFromCategory = (category: string, item: string) =>
    setSelections((p) => {
      const prev = p[category] ?? [];
      if (!prev.length) return p;
      const next = prev.filter((x) => x !== item);
      return { ...p, [category]: next };
    });

  const clearCategory = (category: string) =>
    setSelections((p) => {
      const copy = { ...p };
      delete copy[category];
      return copy;
    });

  const clearAll = () => setSelections({});

  return (
    <BookingSelectionContext.Provider
      value={{ selections, getForCategory, setForCategory, addToCategory, removeFromCategory, clearCategory, clearAll }}
    >
      {children}
    </BookingSelectionContext.Provider>
  );
};

export const useBookingSelection = () => {
  const ctx = useContext(BookingSelectionContext);
  if (!ctx) throw new Error('useBookingSelection must be used within BookingSelectionProvider');
  return ctx;
};

export default BookingSelectionContext;
