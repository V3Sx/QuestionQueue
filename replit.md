# Overview

This is a Portuguese question generator application that provides random conversation-starter questions organized by relationship categories (namorado/boyfriend-girlfriend, amigo/friend, pais/parents). The application features a React frontend with Express backend, using a clean card-based interface for category selection and question display.

# User Preferences

Preferred communication style: Simple, everyday language.

# System Architecture

## Frontend Architecture
- **Framework**: React with TypeScript using Vite as the build tool
- **UI Library**: Shadcn/ui components built on Radix UI primitives
- **Styling**: Tailwind CSS with custom design tokens and CSS variables
- **Typography**: Dual font system - Poppins for headings/UI elements, Inter for body text
- **State Management**: TanStack Query for server state management
- **Routing**: Wouter for lightweight client-side routing
- **Build System**: Vite with custom configuration for development and production builds

The frontend follows a component-driven architecture with reusable UI components in the `/client/src/components/ui` directory. The main application logic resides in `/client/src/pages/home.tsx`, which handles category selection, question fetching, and display logic.

### Design System
- **Primary Colors**: Indigo/Blue theme (#6366F1) for trust and professionalism
- **Typography Hierarchy**: Poppins (modern, geometric) for titles and UI, Inter (optimized for screens) for content
- **Color Palette**: Accessible color combinations meeting WCAG AA standards

## Backend Architecture
- **Framework**: Express.js with TypeScript
- **API Design**: RESTful endpoints for fetching questions and categories
- **Development Setup**: Custom Vite integration for hot reloading in development
- **Build Process**: ESBuild for server-side compilation and bundling

The backend provides three main endpoints:
- `/api/categories` - Returns all categories with question counts
- `/api/questions/:category` - Returns all questions for a specific category
- `/api/questions/:category/random` - Returns a random question from a category

## Data Storage Solutions
- **Database ORM**: Drizzle ORM configured for PostgreSQL
- **Development Storage**: In-memory storage implementation with pre-populated Portuguese questions
- **Database Provider**: Configured for Neon Database (PostgreSQL-compatible)
- **Schema Management**: Drizzle Kit for database migrations and schema management

The application currently uses an in-memory storage adapter (`MemStorage`) for development, with the infrastructure ready for PostgreSQL deployment. Questions are pre-populated across three categories with Portuguese content.

## Authentication and Authorization
- **Current State**: No authentication system implemented
- **Session Management**: Infrastructure present (connect-pg-simple for PostgreSQL sessions) but not actively used
- **Security**: Basic Express security with JSON parsing and URL encoding

## External Dependencies

### Core Framework Dependencies
- React ecosystem (React, React DOM, Vite, TypeScript)
- Express.js for backend API server
- TanStack Query for API state management and caching

### UI and Styling
- Shadcn/ui component library with Radix UI primitives
- Tailwind CSS for utility-first styling
- Lucide React for consistent iconography
- Class Variance Authority (CVA) for component variant management

### Database and ORM
- Drizzle ORM for type-safe database operations
- Drizzle Kit for schema management and migrations
- @neondatabase/serverless for PostgreSQL connectivity
- Drizzle-Zod for schema validation integration

### Development Tools
- ESBuild for production server bundling
- PostCSS with Autoprefixer for CSS processing
- Custom Replit integration plugins for development environment