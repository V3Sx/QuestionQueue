import type { Express } from "express";
import { createServer, type Server } from "http";
import { storage } from "./storage";
import { z } from "zod";

export async function registerRoutes(app: Express): Promise<Server> {
  // Get all questions by category
  app.get("/api/questions/:category", async (req, res) => {
    try {
      const categorySchema = z.enum(["namorado", "amigo", "pais"]);
      const category = categorySchema.parse(req.params.category);
      
      const questions = await storage.getQuestionsByCategory(category);
      res.json(questions);
    } catch (error) {
      res.status(400).json({ message: "Categoria inválida. Use: namorado, amigo ou pais" });
    }
  });

  // Get a random question from a category
  app.get("/api/questions/:category/random", async (req, res) => {
    try {
      const categorySchema = z.enum(["namorado", "amigo", "pais"]);
      const category = categorySchema.parse(req.params.category);
      
      const questions = await storage.getQuestionsByCategory(category);
      
      if (questions.length === 0) {
        return res.status(404).json({ message: "Nenhuma pergunta encontrada para esta categoria" });
      }
      
      const randomIndex = Math.floor(Math.random() * questions.length);
      const randomQuestion = questions[randomIndex];
      
      res.json(randomQuestion);
    } catch (error) {
      res.status(400).json({ message: "Categoria inválida. Use: namorado, amigo ou pais" });
    }
  });

  // Get all categories with question counts
  app.get("/api/categories", async (req, res) => {
    try {
      const categories = [
        {
          id: "namorado",
          name: "Namorado(a)",
          icon: "💕",
          description: "Perguntas íntimas e românticas para fortalecer o relacionamento",
          questionCount: (await storage.getQuestionsByCategory("namorado")).length
        },
        {
          id: "amigo",
          name: "Amigo(a)",
          icon: "🤝",
          description: "Perguntas divertidas e curiosas para conhecer melhor seus amigos",
          questionCount: (await storage.getQuestionsByCategory("amigo")).length
        },
        {
          id: "pais",
          name: "Pais",
          icon: "👨‍👩‍👧‍👦",
          description: "Perguntas respeitosas e reflexivas para conversas em família",
          questionCount: (await storage.getQuestionsByCategory("pais")).length
        }
      ];
      
      res.json(categories);
    } catch (error) {
      res.status(500).json({ message: "Erro interno do servidor" });
    }
  });

  const httpServer = createServer(app);
  return httpServer;
}
