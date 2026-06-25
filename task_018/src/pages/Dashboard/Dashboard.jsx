import styled from 'styled-components';
import { Card, CardTitle, CardHeader } from '../../components/Card/Card';
import { Users, DollarSign, ShoppingCart, Activity } from 'lucide-react';

const Grid = styled.div`
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
`;

const StatValue = styled.h2`
  font-size: 28px;
  font-weight: 700;
  color: ${({ theme }) => theme.colors.text};
  margin-top: 10px;
`;

const IconWrapper = styled.div`
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: ${({ theme }) => theme.colors.secondary};
  color: ${({ theme }) => theme.colors.primary};
  display: flex;
  justify-content: center;
  align-items: center;
`;

const Dashboard = () => {
  const stats = [
    { title: 'Total Users', value: '1,240', icon: Users },
    { title: 'Revenue', value: '$34,000', icon: DollarSign },
    { title: 'Orders', value: '450', icon: ShoppingCart },
    { title: 'Active Sessions', value: '124', icon: Activity },
  ];

  return (
    <div>
      <Grid>
        {stats.map((stat, index) => (
          <Card key={index}>
            <CardHeader>
              <CardTitle>{stat.title}</CardTitle>
              <IconWrapper>
                <stat.icon size={24} />
              </IconWrapper>
            </CardHeader>
            <StatValue>{stat.value}</StatValue>
          </Card>
        ))}
      </Grid>
      
      {/* A placeholder for future charts or more content */}
      <Card style={{ minHeight: '300px', justifyContent: 'center', alignItems: 'center' }}>
        <p style={{ color: '#A3AED0' }}>More analytics content goes here...</p>
      </Card>
    </div>
  );
};

export default Dashboard;
