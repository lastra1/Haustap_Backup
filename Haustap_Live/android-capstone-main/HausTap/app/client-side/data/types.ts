// Shared type for all category arrays
export interface Category {
  title: string;
  price?: string;
  size?: string;
  desc?: string;
  section?: string; // For section headers like 'TERMITES', 'ANTS', etc.
}
