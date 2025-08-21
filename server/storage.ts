import { type User, type InsertUser, type Question, type InsertQuestion } from "@shared/schema";
import { randomUUID } from "crypto";

export interface IStorage {
  getUser(id: string): Promise<User | undefined>;
  getUserByUsername(username: string): Promise<User | undefined>;
  createUser(user: InsertUser): Promise<User>;
  getQuestionsByCategory(category: string): Promise<Question[]>;
  getAllQuestions(): Promise<Question[]>;
  createQuestion(question: InsertQuestion): Promise<Question>;
}

export class MemStorage implements IStorage {
  private users: Map<string, User>;
  private questions: Map<string, Question>;

  constructor() {
    this.users = new Map();
    this.questions = new Map();
    this.initializeQuestions();
  }

  private initializeQuestions() {
    const questionsData = [
      // Namorado(a) questions
      { category: "namorado", content: "O que mais te atrai em mim além da aparência física?" },
      { category: "namorado", content: "Qual foi o momento em que você teve certeza de que me amava?" },
      { category: "namorado", content: "Como você imagina nossa vida daqui a 5 anos?" },
      { category: "namorado", content: "Qual é a sua lembrança favorita de nós dois juntos?" },
      { category: "namorado", content: "O que você mais admira na nossa relação?" },
      { category: "namorado", content: "Se pudéssemos viajar para qualquer lugar do mundo juntos, para onde iríamos?" },
      { category: "namorado", content: "Qual é o seu sonho que ainda não me contou?" },
      { category: "namorado", content: "Como você se sente quando estamos separados por um tempo?" },
      { category: "namorado", content: "O que você acha que precisamos melhorar no nosso relacionamento?" },
      { category: "namorado", content: "Qual foi o maior obstáculo que já superamos juntos?" },
      { category: "namorado", content: "Como você gostaria de ser surpreendido(a) por mim?" },
      { category: "namorado", content: "Qual é a coisa mais romântica que já fizemos juntos?" },
      { category: "namorado", content: "O que você sente quando me vê pela primeira vez no dia?" },
      { category: "namorado", content: "Qual é o seu maior medo em relação ao nosso futuro?" },
      { category: "namorado", content: "Como você descreveria nosso amor para alguém que nunca se apaixonou?" },

      // Amigo(a) questions
      { category: "amigo", content: "Qual foi o momento mais engraçado que vivemos juntos?" },
      { category: "amigo", content: "Se pudesse me dar um superpoder, qual seria e por quê?" },
      { category: "amigo", content: "Qual é a coisa mais maluca que você faria se soubesse que não haveria consequências?" },
      { category: "amigo", content: "Que conselho você daria para o seu eu de 10 anos atrás?" },
      { category: "amigo", content: "Qual é o seu maior talento escondido?" },
      { category: "amigo", content: "Se você pudesse jantar com qualquer pessoa da história, quem seria?" },
      { category: "amigo", content: "Qual é a sua maior fobia ou medo irracional?" },
      { category: "amigo", content: "Se pudéssemos fazer uma viagem de aventura juntos, para onde iríamos?" },
      { category: "amigo", content: "Qual é a coisa mais estranha que você já comeu?" },
      { category: "amigo", content: "Se você pudesse ter qualquer profissão por um dia, qual escolheria?" },
      { category: "amigo", content: "Qual é o filme que você assistiria mil vezes sem se cansar?" },
      { category: "amigo", content: "Que habilidade você gostaria de aprender do zero?" },
      { category: "amigo", content: "Qual é a sua teoria conspiratória favorita?" },
      { category: "amigo", content: "Se você fosse um personagem de filme, de qual filme seria?" },
      { category: "amigo", content: "Qual é a coisa mais espontânea que você já fez na vida?" },

      // Pais questions
      { category: "pais", content: "Qual foi o momento em que vocês se sentiram mais orgulhosos de mim?" },
      { category: "pais", content: "Que valores vocês consideram mais importantes para transmitir?" },
      { category: "pais", content: "Qual foi a lição mais importante que aprenderam sendo pais?" },
      { category: "pais", content: "Como era a vida de vocês antes de eu nascer?" },
      { category: "pais", content: "Qual é a sua maior esperança para o meu futuro?" },
      { category: "pais", content: "Que tradição familiar vocês mais valorizam?" },
      { category: "pais", content: "Qual foi o maior desafio que enfrentaram como pais?" },
      { category: "pais", content: "Como vocês se conheceram e se apaixonaram?" },
      { category: "pais", content: "Que conselho vocês dariam para pais de primeira viagem?" },
      { category: "pais", content: "Qual é a memória mais especial da minha infância para vocês?" },
      { category: "pais", content: "Como vocês esperam que nossa família evolua com o tempo?" },
      { category: "pais", content: "Qual foi o momento mais assustador da paternidade/maternidade?" },
      { category: "pais", content: "Que sonho vocês ainda querem realizar juntos?" },
      { category: "pais", content: "Como vocês equilibram amor e disciplina na educação?" },
      { category: "pais", content: "Qual é a coisa que vocês mais querem que eu entenda sobre a vida?" }
    ];

    questionsData.forEach(q => {
      const id = randomUUID();
      const question: Question = { id, ...q };
      this.questions.set(id, question);
    });
  }

  async getUser(id: string): Promise<User | undefined> {
    return this.users.get(id);
  }

  async getUserByUsername(username: string): Promise<User | undefined> {
    return Array.from(this.users.values()).find(
      (user) => user.username === username,
    );
  }

  async createUser(insertUser: InsertUser): Promise<User> {
    const id = randomUUID();
    const user: User = { ...insertUser, id };
    this.users.set(id, user);
    return user;
  }

  async getQuestionsByCategory(category: string): Promise<Question[]> {
    return Array.from(this.questions.values()).filter(
      (question) => question.category === category
    );
  }

  async getAllQuestions(): Promise<Question[]> {
    return Array.from(this.questions.values());
  }

  async createQuestion(insertQuestion: InsertQuestion): Promise<Question> {
    const id = randomUUID();
    const question: Question = { ...insertQuestion, id };
    this.questions.set(id, question);
    return question;
  }
}

export const storage = new MemStorage();
