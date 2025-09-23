# 🎨 GymGenius Frontend (Next.js + shadcn/ui + TailwindCSS)

## 📌 Overview

The frontend for **GymGenius** is built with **Next.js**, styled using **TailwindCSS**, and leverages **shadcn/ui** for accessible, modern UI components. It connects with the Spring Boot backend to deliver a seamless fitness tracking experience.

---

## 🔹 Core Features (UI)

### 1. Authentication & User Profiles

* Sign up / Login pages.
* Profile page with user details (weight, height, age, goal).
* JWT stored securely (httpOnly cookies).

### 2. Training Splits & Workouts

* Dashboard with training templates (Push/Pull/Legs, Upper/Lower).
* Custom workout builder UI.
* Exercise logging (sets, reps, weights).

### 3. Progress Tracking

* Graphs for **weight over time**.
* PR highlights (best lifts).
* Calendar view of past workouts.

### 4. Nutrition *(Optional)*

* Daily calorie & macro goals UI.
* Meal logging with macro breakdown.

---

## 🛠️ Tech Stack

* **Next.js 14+ (App Router)**
* **TailwindCSS** (utility-first styling)
* **shadcn/ui** (prebuilt accessible components)
* **React Query (API state management)
* **Axios** (API client)

---

## 📂 Project Structure

```
gymgenius-frontend/
 ┣ 📂 app             # Next.js App Router pages
 ┣ 📂 components     # UI Components
 ┃ ┣ 📂 ui           # Reusable UI components (shadcn)
 ┣ 📂 hooks          # Custom React hooks (API, auth)
 ┣ 📂 lib            # Utilities (axios instance, helpers)
 ┣ 📂 styles         # Tailwind global styles
 ┣ tailwind.config.js
 ┣ package.json
 ┗ tsconfig.json
```

---

## 🎨 UI Guidelines

* **shadcn/ui** for buttons, inputs, cards, modals.
* **TailwindCSS** for layout + responsive design.
* Keep UI **minimal, modern, mobile-first**.

---

## 🔄 Git Workflow (PR Strategy)

### Branching Model

* **master** → stable production-ready code.
* **dev** → active development branch.
* **feature/**\* → feature branches branched from `dev`.


### Commit Message Convention

```
<type>(scope): short description
```

**Types:**

* `feat` → new feature
* `fix` → bug fix
* `docs` → documentation only
* `style` → formatting, missing semi-colons, etc.
* `refactor` → code restructuring
* `test` → adding/updating tests
* `chore` → maintenance, CI/CD, tooling

✅ Example:

```
feat(workouts): add custom workout builder page
fix(auth): resolve JWT expiration handling
```

---

## ✅ Next Steps

* Scaffold UI with **Next.js App Router**.
* Integrate **auth API** (JWT with backend).
* Build dashboard & workout logging flows.
* Add charts with **Recharts or Victory** for progress.
* Enforce PR workflow with GitHub branch protections.
