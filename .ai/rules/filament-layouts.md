# Filament Resource UI & Layout Standards

All Filament resources across the application must strictly follow this visual and structural blueprint.

## 1. Slide-Over Modals (Width & Proportions)
- **Modal Width Rule**: Never use narrow modal widths like `Width::Large` or `Width::Medium` for resource create/edit slide-overs. They compress form fields into single vertical stripes.
- **Standard Width**: Always use `Width::FiveExtraLarge` (or `Width::SevenExtraLarge` for rich media/spec sheets):
  ```php
  use Filament\Support\Enums\Width;

  CreateAction::make()
      ->slideOver()
      ->modalWidth(Width::FiveExtraLarge);

  EditAction::make()
      ->slideOver()
      ->modalWidth(Width::FiveExtraLarge);
  ```

## 2. Form Schema Architecture (Grid & Sections)
- **Top-Level Columns**: Always set `->columns(12)` on the root `$schema`.
- **Primary / Secondary Two-Column Split**:
  - **Left / Primary Column** (`columnSpan: ['default' => 12, 'md' => 7, 'xl' => 8]`):
    Core fields, name, category, numbers, description.
  - **Right / Secondary Column** (`columnSpan: ['default' => 12, 'md' => 5, 'xl' => 4]`):
    Metadata, image/media, dates, reference codes, toggles, badges.
- **Internal Sub-Grids**: Inside sections, use `Grid::make(['default' => 1, 'sm' => 2])` for paired numeric or date inputs so labels and controls breathe comfortably.

## 3. List Page Overview Widgets
- Every resource index page should display an overview Stats widget at the top (`getHeaderWidgets()`) with key metrics (Total counts, active states, inventory balances, averages).

## 4. Table Design
- Use bold typography for primary identifiers.
- Use badges with semantic colors (`success`, `warning`, `danger`, `primary`).
- Slide-over action modals for Edit / View.
