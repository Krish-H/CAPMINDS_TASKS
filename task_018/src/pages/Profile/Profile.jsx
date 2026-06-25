import { useState } from 'react';
import styled from 'styled-components';
import { Card, CardTitle, CardHeader } from '../../components/Card/Card';
import Button from '../../components/Button/Button';
import Input from '../../components/Input/Input';
import Modal from '../../components/Modal/Modal';
import { Edit2, Camera } from 'lucide-react';

const ProfileWrapper = styled.div`
  max-width: 800px;
  margin: 0 auto;
`;

const AvatarSection = styled.div`
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 30px;
`;

const Avatar = styled.div`
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: ${({ theme }) => theme.colors.primary};
  display: flex;
  justify-content: center;
  align-items: center;
  color: #fff;
  font-size: 36px;
  font-weight: 700;
  position: relative;
`;

const AvatarBadge = styled.div`
  position: absolute;
  bottom: 0;
  right: 0;
  background: ${({ theme }) => theme.colors.surface};
  color: ${({ theme }) => theme.colors.textSecondary};
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  box-shadow: 0px 2px 5px rgba(0,0,0,0.1);
  cursor: pointer;
`;

const InfoGrid = styled.div`
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;

  @media (max-width: 600px) {
    grid-template-columns: 1fr;
  }
`;

const InfoItem = styled.div`
  display: flex;
  flex-direction: column;
  gap: 5px;
`;

const InfoLabel = styled.span`
  font-size: 14px;
  color: ${({ theme }) => theme.colors.textSecondary};
`;

const InfoValue = styled.span`
  font-size: 16px;
  font-weight: 600;
  color: ${({ theme }) => theme.colors.text};
`;

const Profile = () => {
  const [isEditing, setIsEditing] = useState(false);
  const [profile, setProfile] = useState({
    firstName: 'John',
    lastName: 'Doe',
    email: 'john.doe@example.com',
    role: 'Administrator',
    phone: '+1 234 567 8900'
  });

  const [editForm, setEditForm] = useState(profile);

  const handleSave = (e) => {
    e.preventDefault();
    setProfile(editForm);
    setIsEditing(false);
  };

  return (
    <ProfileWrapper>
      <Card>
        <CardHeader>
          <CardTitle>User Profile</CardTitle>
          <Button variant="secondary" icon={Edit2} onClick={() => setIsEditing(true)}>
            Edit Profile
          </Button>
        </CardHeader>
        
        <AvatarSection>
          <Avatar>
            {profile.firstName[0]}{profile.lastName[0]}
            <AvatarBadge>
              <Camera size={14} />
            </AvatarBadge>
          </Avatar>
          <div>
            <h2 style={{ fontSize: '24px', marginBottom: '5px' }}>
              {profile.firstName} {profile.lastName}
            </h2>
            <p style={{ color: '#A3AED0' }}>{profile.role}</p>
          </div>
        </AvatarSection>

        <InfoGrid>
          <InfoItem>
            <InfoLabel>First Name</InfoLabel>
            <InfoValue>{profile.firstName}</InfoValue>
          </InfoItem>
          <InfoItem>
            <InfoLabel>Last Name</InfoLabel>
            <InfoValue>{profile.lastName}</InfoValue>
          </InfoItem>
          <InfoItem>
            <InfoLabel>Email Address</InfoLabel>
            <InfoValue>{profile.email}</InfoValue>
          </InfoItem>
          <InfoItem>
            <InfoLabel>Phone Number</InfoLabel>
            <InfoValue>{profile.phone}</InfoValue>
          </InfoItem>
        </InfoGrid>
      </Card>

      <Modal isOpen={isEditing} onClose={() => setIsEditing(false)} title="Edit Profile">
        <form onSubmit={handleSave} style={{ display: 'flex', flexDirection: 'column', gap: '15px', marginTop: '20px' }}>
          <InfoGrid>
            <Input 
              label="First Name" 
              value={editForm.firstName} 
              onChange={(e) => setEditForm({...editForm, firstName: e.target.value})}
              required
            />
            <Input 
              label="Last Name" 
              value={editForm.lastName} 
              onChange={(e) => setEditForm({...editForm, lastName: e.target.value})}
              required
            />
            <Input 
              label="Email" 
              type="email"
              value={editForm.email} 
              onChange={(e) => setEditForm({...editForm, email: e.target.value})}
              required
            />
            <Input 
              label="Phone" 
              value={editForm.phone} 
              onChange={(e) => setEditForm({...editForm, phone: e.target.value})}
            />
          </InfoGrid>
          <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '10px', marginTop: '10px' }}>
            <Button type="button" variant="secondary" onClick={() => setIsEditing(false)}>Cancel</Button>
            <Button type="submit">Save Changes</Button>
          </div>
        </form>
      </Modal>
    </ProfileWrapper>
  );
};

export default Profile;
