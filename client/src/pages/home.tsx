import { useState } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Separator } from "@/components/ui/separator";
import { RotateCcw, ArrowLeft, MessageCircle, Users, Heart } from "lucide-react";
import { apiRequest } from "@/lib/queryClient";
import { ThemeToggle } from "@/components/theme-toggle";

interface Category {
  id: string;
  name: string;
  icon: string;
  description: string;
  questionCount: number;
}

interface Question {
  id: string;
  category: string;
  content: string;
}

export default function Home() {
  const [selectedCategory, setSelectedCategory] = useState<string | null>(null);
  const [currentQuestion, setCurrentQuestion] = useState<Question | null>(null);
  const [usedQuestionIds, setUsedQuestionIds] = useState<Set<string>>(new Set());
  const queryClient = useQueryClient();

  // Fetch categories
  const { data: categories, isLoading: categoriesLoading } = useQuery<Category[]>({
    queryKey: ["/api/categories"],
  });

  // Fetch questions for selected category
  const { data: categoryQuestions } = useQuery<Question[]>({
    queryKey: ["/api/questions", selectedCategory],
    enabled: !!selectedCategory,
  });

  // Random question mutation
  const randomQuestionMutation = useMutation({
    mutationFn: async (category: string) => {
      const response = await apiRequest("GET", `/api/questions/${category}/random`);
      return response.json();
    },
    onSuccess: (question: Question) => {
      setCurrentQuestion(question);
      setUsedQuestionIds(prev => new Set(prev).add(question.id));
    },
  });

  const generateNewQuestion = () => {
    if (!selectedCategory || !categoryQuestions) return;

    // Filter out used questions
    const availableQuestions = categoryQuestions.filter(q => !usedQuestionIds.has(q.id));

    // If all questions have been used, reset
    if (availableQuestions.length === 0) {
      setUsedQuestionIds(new Set());
      const randomQuestion = categoryQuestions[Math.floor(Math.random() * categoryQuestions.length)];
      setCurrentQuestion(randomQuestion);
      setUsedQuestionIds(new Set([randomQuestion.id]));
      return;
    }

    // Get random from available questions
    const randomQuestion = availableQuestions[Math.floor(Math.random() * availableQuestions.length)];
    setCurrentQuestion(randomQuestion);
    setUsedQuestionIds(prev => new Set(prev).add(randomQuestion.id));
  };

  const selectCategory = (categoryId: string) => {
    setSelectedCategory(categoryId);
    setUsedQuestionIds(new Set());
    setCurrentQuestion(null);
    randomQuestionMutation.mutate(categoryId);
  };

  const goBackToCategories = () => {
    setSelectedCategory(null);
    setCurrentQuestion(null);
    setUsedQuestionIds(new Set());
  };

  const getCategoryConfig = (categoryId: string) => {
    const icons: Record<string, string> = {
      namorado: "💕",
      amigo: "🤝",
      pais: "👨‍👩‍👧‍👦"
    };
    return icons[categoryId] || "💬";
  };

  if (categoriesLoading) {
    return (
      <div className="min-h-screen bg-bg-light flex items-center justify-center">
        <div className="text-center">
          <div className="w-12 h-12 border-4 border-primary border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
          <p className="text-text-secondary">Carregando...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-bg-light">
      {/* Header */}
      <header className="bg-white shadow-sm border-b border-gray-100">
        <div className="max-w-4xl mx-auto px-4 py-6">
          <div className="text-center">
            <h1 className="text-3xl md:text-4xl font-bold font-heading text-text-primary mb-2">
              💬 Gerador de Perguntas
            </h1>
            <p className="text-text-secondary text-lg">
              Conecte-se através de conversas significativas
            </p>
          </div>
          {/* Theme Toggle Button - added to the header */}
          <div className="flex justify-end mb-4">
            <ThemeToggle />
          </div>
        </div>
      </header>

      <main className="max-w-4xl mx-auto px-4 py-8">
        {!selectedCategory ? (
          /* Category Selection */
          <section className="mb-8">
            <h2 className="text-2xl font-semibold font-heading text-text-primary mb-6 text-center">
              Escolha quem irá responder as perguntas
            </h2>

            <div className="grid md:grid-cols-3 gap-6">
              {categories?.map((category) => (
                <Card 
                  key={category.id}
                  className="cursor-pointer transition-all duration-300 hover:shadow-lg hover:border-primary hover:-translate-y-1"
                  onClick={() => selectCategory(category.id)}
                >
                  <CardContent className="p-6 text-center">
                    <div className="w-16 h-16 bg-primary-light rounded-full flex items-center justify-center mx-auto mb-4">
                      <span className="text-3xl">{category.icon}</span>
                    </div>
                    <h3 className="text-xl font-semibold font-heading text-text-primary mb-2">
                      {category.name}
                    </h3>
                    <p className="text-text-secondary mb-4">
                      {category.description}
                    </p>
                    <div className="inline-flex items-center text-primary font-medium">
                      <span>{category.questionCount} perguntas disponíveis</span>
                    </div>
                  </CardContent>
                </Card>
              ))}
            </div>
          </section>
        ) : (
          /* Question Display */
          <section>
            <Card className="shadow-lg p-8 mb-6">
              <div className="text-center mb-6">
                <div className="inline-flex items-center bg-primary-light px-4 py-2 rounded-full mb-4">
                  <span className="text-2xl mr-2">
                    {getCategoryConfig(selectedCategory)}
                  </span>
                  <span className="font-medium text-primary">
                    {categories?.find(c => c.id === selectedCategory)?.name}
                  </span>
                </div>
              </div>

              <div className="text-center">
                <div className="w-20 h-20 bg-gradient-to-br from-primary to-primary-hover rounded-full flex items-center justify-center mx-auto mb-6">
                  <MessageCircle className="w-10 h-10 text-white" />
                </div>

                <h3 className="text-2xl md:text-3xl font-medium font-sans text-text-primary mb-6 leading-relaxed min-h-[4rem] flex items-center justify-center">
                  {randomQuestionMutation.isPending ? (
                    <div className="flex items-center space-x-2">
                      <div className="w-2 h-2 bg-primary rounded-full animate-bounce"></div>
                      <div className="w-2 h-2 bg-primary rounded-full animate-bounce" style={{ animationDelay: '0.1s' }}></div>
                      <div className="w-2 h-2 bg-primary rounded-full animate-bounce" style={{ animationDelay: '0.2s' }}></div>
                    </div>
                  ) : (
                    currentQuestion?.content || "Gerando pergunta..."
                  )}
                </h3>

                <div className="flex flex-col sm:flex-row gap-4 justify-center items-center">
                  <Button 
                    onClick={generateNewQuestion}
                    disabled={randomQuestionMutation.isPending}
                    className="bg-primary hover:bg-primary-hover text-white font-semibold font-heading py-3 px-8 rounded-lg transition-colors duration-200 flex items-center space-x-2"
                  >
                    <RotateCcw className="w-5 h-5" />
                    <span>Nova Pergunta</span>
                  </Button>

                  <Button 
                    onClick={goBackToCategories}
                    variant="outline"
                    className="bg-gray-100 hover:bg-gray-200 text-text-primary font-semibold font-heading py-3 px-8 rounded-lg transition-colors duration-200 flex items-center space-x-2"
                  >
                    <ArrowLeft className="w-5 h-5" />
                    <span>Escolher Outra Categoria</span>
                  </Button>
                </div>
              </div>
            </Card>

            {/* Question Counter */}
            <div className="text-center">
              <div className="inline-flex items-center bg-white rounded-full px-4 py-2 shadow-sm">
                <span className="text-text-secondary text-sm">
                  Pergunta <span className="font-medium text-primary">{usedQuestionIds.size}</span> 
                  {" "}de <span className="font-medium">{categoryQuestions?.length || 15}</span>
                </span>
              </div>
            </div>
          </section>
        )}

        {/* Features Section */}
        <section className="mt-16 text-center">
          <h3 className="text-xl font-semibold font-heading text-text-primary mb-6">
            Por que usar nosso gerador?
          </h3>
          <div className="grid md:grid-cols-3 gap-6">
            <Card className="p-6">
              <div className="w-12 h-12 bg-primary-light rounded-lg flex items-center justify-center mx-auto mb-4">
                <Users className="w-6 h-6 text-primary" />
              </div>
              <h4 className="font-semibold font-heading text-text-primary mb-2">
                Fortalece Relacionamentos
              </h4>
              <p className="text-text-secondary text-sm">
                Perguntas cuidadosamente elaboradas para cada tipo de relacionamento
              </p>
            </Card>

            <Card className="p-6">
              <div className="w-12 h-12 bg-primary-light rounded-lg flex items-center justify-center mx-auto mb-4">
                <MessageCircle className="w-6 h-6 text-primary" />
              </div>
              <h4 className="font-semibold font-heading text-text-primary mb-2">
                Conversas Significativas
              </h4>
              <p className="text-text-secondary text-sm">
                Saia do superficial e descubra coisas novas sobre as pessoas queridas
              </p>
            </Card>

            <Card className="p-6">
              <div className="w-12 h-12 bg-primary-light rounded-lg flex items-center justify-center mx-auto mb-4">
                <Heart className="w-6 h-6 text-primary" />
              </div>
              <h4 className="font-semibold font-heading text-text-primary mb-2">
                Fácil de Usar
              </h4>
              <p className="text-text-secondary text-sm">
                Interface simples e intuitiva, funciona em qualquer dispositivo
              </p>
            </Card>
          </div>
        </section>
      </main>

      {/* Footer */}
      <footer className="bg-white border-t border-gray-100 mt-16">
        <div className="max-w-4xl mx-auto px-4 py-8 text-center">
          <p className="text-text-secondary">
            💕 Feito com carinho para fortalecer relacionamentos
          </p>
        </div>
      </footer>
    </div>
  );
}